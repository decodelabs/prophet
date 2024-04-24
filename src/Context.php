<?php

/**
 * @package Prophet
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Prophet;

use DecodeLabs\Coercion;
use DecodeLabs\Prophet;
use DecodeLabs\Prophet\Model\Assistant;
use DecodeLabs\Prophet\Model\Repository;
use DecodeLabs\Prophet\Model\Thread;
use DecodeLabs\Slingshot;
use DecodeLabs\Veneer;

/**
 * @template A of Assistant
 * @template T of Thread
 * @template S of Subject
 */
class Context
{
    /**
     * @var Repository<A, T>|null
     */
    protected ?Repository $repository = null;
    protected Slingshot $slingshot;

    public function __construct(
        ?Slingshot $slingshot = null
    ) {
        $this->slingshot = $slingshot ?? new Slingshot();
    }

    /**
     * Get the repository instance
     *
     * @return Repository<A, T>
     */
    public function getRepository(): Repository
    {
        if ($this->repository === null) {
            $this->repository = $this->slingshot->resolveInstance(Repository::class);
        }

        return $this->repository;
    }

    /**
     * Load assistant
     *
     * @param string|Blueprint<S> $blueprint
     */
    public function loadAssistant(
        string|Blueprint $blueprint,
        string $serviceName
    ): Assistant {
        $repository = $this->getRepository();
        $blueprint = $this->normalizeBlueprint($blueprint);
        $assistant = $repository->fetchAssistant($blueprint, $serviceName);
        $platform = $this->loadPlatform($serviceName);
        $store = false;

        if (!$assistant) {
            $assistant = $repository->createAssistant($blueprint, $serviceName);
            $platform->findAssistant($assistant);
            $store = true;
        }

        if ($assistant->getLanguageModelName() === null) {
            $assistant->setLanguageModelName(
                $platform->suggestModel(
                    $blueprint->getMedium(),
                    $blueprint->getLanguageModelLevel(),
                    $blueprint->getFeatures()
                )
            );
            $store = true;
        }

        if ($assistant->getServiceId() === null) {
            $platform->createAssistant($assistant);
            $store = true;
        }

        if ($store) {
            $repository->storeAssistant($assistant);
        }

        return $assistant;
    }


    /**
     * Load a thread
     *
     * @param string|Blueprint<S> $blueprint
     * @param S $subject
     */
    public function loadThread(
        string|Blueprint $blueprint,
        Subject $subject
    ): Thread {
        $repository = $this->getRepository();
        $blueprint = $this->normalizeBlueprint($blueprint);

        if (!$thread = $repository->fetchThread($blueprint, $subject)) {
            $thread = $repository->createThread($blueprint, $subject);
        }

        $assistant = $this->loadAssistant($blueprint, $thread->getServiceName());
        $platform = $this->loadPlatform($thread->getServiceName());

        if ($thread->getServiceId() !== null) {
            if (!$thread->isReady()) {
                $platform->refreshThread($thread);
            }
        } else {
            $platform->startThread(
                $assistant,
                $thread,
                $blueprint->generateAdditionalInstructions($subject)
            );
        }

        $repository->storeThread($thread);
        return $thread;
    }

    /**
     * @param string|Blueprint<S> $blueprint
     * @return Blueprint<S>
     */
    protected function normalizeBlueprint(
        string|Blueprint $blueprint
    ): Blueprint {
        if (is_string($blueprint)) {
            $blueprint = $this->slingshot->resolveNamedInstance(Blueprint::class, $blueprint);
        }

        return $blueprint;
    }

    /**
     * Serialize thread with messages
     *
     * @return array<string, mixed>
     */
    public function serializeThreadWithMessages(
        Thread $thread,
        ?string $afterId = null,
        int $limit = 20
    ): array {
        return array_merge(
            Coercion::toArray($thread->jsonSerialize()),
            $this->fetchMessages($thread, $afterId, $limit)
        );
    }

    /**
     * Fetch messages
     *
     * @return array<string, mixed>
     */
    public function fetchMessages(
        Thread $thread,
        ?string $afterId = null,
        int $limit = 20
    ): array {
        if ($thread->getServiceId() === null) {
            return [];
        }

        $platform = $this->loadPlatform($thread->getServiceName());
        return $platform->fetchMessages($thread, $afterId, $limit);
    }


    /**
     * Load platform
     */
    public function loadPlatform(
        string $name
    ): Platform {
        return $this->slingshot->resolveNamedInstance(Platform::class, $name);
    }
}


// Register the Veneer facade
Veneer::register(Context::class, Prophet::class);
