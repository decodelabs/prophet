<?php

/**
 * @package Prophet
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Prophet;

use DecodeLabs\Coercion;
use DecodeLabs\Dictum;
use DecodeLabs\Exceptional;
use DecodeLabs\Prophet;
use DecodeLabs\Prophet\Model\Assistant;
use DecodeLabs\Prophet\Model\Message;
use DecodeLabs\Prophet\Model\MessageList;
use DecodeLabs\Prophet\Model\Suggestion;
use DecodeLabs\Prophet\Model\Thread;
use DecodeLabs\Slingshot;
use DecodeLabs\Veneer;

/**
 * @template A of Assistant
 * @template T of Thread
 * @template S of Suggestion
 * @template J of Subject
 */
class Context
{
    /**
     * @var Repository<A,T,S>|null
     */
    protected ?Repository $repository = null;
    protected Slingshot $slingshot;

    public function __construct(
        ?Slingshot $slingshot = null
    ) {
        $this->slingshot = $slingshot ?? new Slingshot();
    }

    /**
     * Load platform
     */
    public function loadPlatform(
        string $name
    ): Platform {
        return $this->slingshot->resolveNamedInstance(Platform::class, $name);
    }

    /**
     * Get the repository instance
     *
     * @return Repository<A,T,S>
     */
    public function getRepository(): Repository
    {
        if ($this->repository === null) {
            $this->repository = $this->slingshot->resolveInstance(Repository::class);
        }

        return $this->repository;
    }

    /**
     * Load assistant if exists
     *
     * @param string|Blueprint<J> $blueprint
     */
    public function tryLoadAssistant(
        string|Blueprint $blueprint,
        string $serviceName,
    ): ?Assistant {
        return $this->loadOrCreateAssistant(
            blueprint: $blueprint,
            serviceName: $serviceName,
            create: false
        );
    }

    /**
     * Load assistant
     *
     * @param string|Blueprint<J> $blueprint
     */
    public function loadAssistant(
        string|Blueprint $blueprint,
        string $serviceName
    ): Assistant {
        return $this->loadOrCreateAssistant(
            blueprint: $blueprint,
            serviceName: $serviceName,
            create: true
        );
    }

    /**
     * Load assistant for new thread
     *
     * @param string|Blueprint<J> $blueprint
     */
    public function loadFreshAssistant(
        string|Blueprint $blueprint,
        string $serviceName
    ): Assistant {
        return $this->loadOrCreateAssistant(
            blueprint: $blueprint,
            serviceName: $serviceName,
            create: true,
            fresh: true
        );
    }


    /**
     * Load assistant
     *
     * @param string|Blueprint<J> $blueprint
     * @phpstan-return ($create is true ? A : A|null)
     */
    protected function loadOrCreateAssistant(
        string|Blueprint $blueprint,
        string $serviceName,
        bool $create,
        bool $fresh = false
    ): ?Assistant {
        $repository = $this->getRepository();
        $blueprint = $this->normalizeBlueprint($blueprint);
        $assistant = $repository->fetchAssistant($blueprint, $serviceName);
        $platform = $this->loadPlatform($serviceName);
        $store = false;

        if (!$assistant) {
            if (!$create) {
                return null;
            }

            $assistant = $repository->createAssistant($blueprint, $serviceName);
            $platform->findAssistant($assistant);
            $store = true;
            $fresh = false;
        }

        $existingModel = $assistant->getLanguageModelName();

        if ($existingModel === null) {
            $assistant->setLanguageModelName(
                $existingModel = $platform->suggestModel(
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
        } elseif (
            $fresh &&
            $existingModel !== ($model = $platform->suggestModel(
                $blueprint->getMedium(),
                $blueprint->getLanguageModelLevel(),
                $blueprint->getFeatures()
            )) &&
            $platform->shouldUpdateModel(
                $existingModel,
                $model,
                $blueprint->getMedium(),
                $blueprint->getLanguageModelLevel(),
                $blueprint->getFeatures()
            )
        ) {
            $assistant->setLanguageModelName($model);

            if ($platform->updateAssistant($assistant)) {
                $store = true;
            }
        }

        if ($store) {
            $repository->storeAssistant($assistant);
        }

        return $assistant;
    }

    /**
     * Update assistant
     *
     * @param A $assistant
     */
    public function updateAssistant(
        Assistant $assistant
    ): void {
        $platform = $this->loadPlatform($assistant->getServiceName());

        if (!$platform->updateAssistant($assistant)) {
            throw Exceptional::Runtime(
                'Failed to update assistant'
            );
        }

        $this->getRepository()->storeAssistant($assistant);
    }

    /**
     * Delete assistant
     *
     * @param string|Blueprint<J> $blueprint
     */
    public function loadAndDeleteAssistant(
        string|Blueprint $blueprint,
        string $serviceName
    ): bool {
        $assistant = $this->loadOrCreateAssistant(
            blueprint: $blueprint,
            serviceName: $serviceName,
            create: false
        );

        if (!$assistant) {
            return false;
        }

        return $this->deleteAssistant($assistant);
    }

    /**
     * Delete assistant
     *
     * @param A $assistant
     */
    public function deleteAssistant(
        Assistant $assistant
    ): bool {
        if ($assistant->getServiceId() !== null) {
            $platform = $this->loadPlatform($assistant->getServiceName());
            $platform->deleteAssistant($assistant);
        }

        $repository = $this->getRepository();
        return $repository->deleteAssistant($assistant);
    }



    /**
     * Load a thread
     *
     * @param string|Blueprint<J> $blueprint
     * @param J $subject
     */
    public function tryLoadThread(
        string|Blueprint $blueprint,
        Subject $subject
    ): ?Thread {
        return $this->loadOrCreateThread(
            blueprint: $blueprint,
            subject: $subject,
            create: false
        );
    }


    /**
     * Load a thread
     *
     * @param string|Blueprint<J> $blueprint
     * @param J $subject
     */
    public function loadThread(
        string|Blueprint $blueprint,
        Subject $subject
    ): Thread {
        return $this->loadOrCreateThread(
            blueprint: $blueprint,
            subject: $subject,
            create: true
        );
    }

    /**
     * Load a thread
     *
     * @param string|Blueprint<J> $blueprint
     * @param J $subject
     * @phpstan-return ($create is true ? T : T|null)
     */
    protected function loadOrCreateThread(
        string|Blueprint $blueprint,
        Subject $subject,
        bool $create
    ): ?Thread {
        $repository = $this->getRepository();
        $blueprint = $this->normalizeBlueprint($blueprint);
        $store = false;

        if (!$thread = $repository->fetchThread($blueprint, $subject)) {
            if (!$create) {
                return null;
            }

            $thread = $repository->createThread($blueprint, $subject);
            $store = true;
        }

        $assistant = $this->loadFreshAssistant($blueprint, $thread->getServiceName());
        $platform = $this->loadPlatform($thread->getServiceName());


        if ($thread->getServiceId() !== null) {
            if (!$thread->isReady()) {
                $platform->refreshThread($thread);
                $store = true;
            }
        } else {
            $platform->startThread(
                $assistant,
                $thread,
                $blueprint->generateAdditionalInstructions($subject)
            );
            $store = true;
        }

        if ($store) {
            $repository->storeThread($thread);
        }

        return $thread;
    }

    /**
     * Refresh a thread
     *
     * @param T $thread
     */
    public function refreshThread(
        Thread $thread
    ): void {
        if ($thread->getServiceId() === null) {
            throw Exceptional::Runtime(
                'Cannot refresh thread that has not completed initialization'
            );
        }

        $platform = $this->loadPlatform($thread->getServiceName());
        $platform->refreshThread($thread);

        $this->getRepository()->storeThread($thread);
    }

    /**
     * delete a thread
     *
     * @param string|Blueprint<J> $blueprint
     * @param J $subject
     */
    public function loadAndDeleteThread(
        string|Blueprint $blueprint,
        Subject $subject
    ): bool {
        $thread = $this->loadOrCreateThread(
            blueprint: $blueprint,
            subject: $subject,
            create: false
        );

        if (!$thread) {
            return false;
        }

        return $this->deleteThread($thread);
    }

    /**
     * Delete a thread
     *
     * @param T $thread
     */
    public function deleteThread(
        Thread $thread
    ): bool {
        if ($thread->getServiceId() !== null) {
            $platform = $this->loadPlatform($thread->getServiceName());
            $platform->deleteThread($thread);
        }

        $repository = $this->getRepository();
        return $repository->deleteThread($thread);
    }

    /**
     * @param string|Blueprint<J> $blueprint
     * @return Blueprint<J>
     */
    protected function normalizeBlueprint(
        string|Blueprint $blueprint
    ): Blueprint {
        if (is_string($blueprint)) {
            $blueprint = Dictum::id($blueprint);
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
        int $limit = 20,
        ?string $afterId = null
    ): array {
        return array_merge(
            Coercion::toArray($thread->jsonSerialize()),
            $this->fetchMessages($thread, $limit, $afterId)->jsonSerialize()
        );
    }

    /**
     * Fetch messages
     */
    public function fetchMessages(
        Thread $thread,
        int $limit = 20,
        ?string $afterId = null
    ): MessageList {
        if ($thread->getServiceId() === null) {
            return new MessageList();
        }

        $platform = $this->loadPlatform($thread->getServiceName());
        return $platform->fetchMessages($thread, $limit, $afterId);
    }

    /**
     * Send reply to thread
     *
     * @param T $thread
     */
    public function reply(
        Thread $thread,
        string $message
    ): Message {
        if ($thread->getServiceId() === null) {
            throw Exceptional::Runtime(
                'Cannot reply to thread that has not completed initialization'
            );
        }

        $assistant = $this->loadAssistant($thread->getAction(), $thread->getServiceName());
        $platform = $this->loadPlatform($thread->getServiceName());
        $output = $platform->reply($assistant, $thread, $message);

        $repository = $this->getRepository();
        $repository->storeThread($thread);

        return $output;
    }
}


// Register the Veneer facade
Veneer::register(Context::class, Prophet::class);
