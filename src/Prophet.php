<?php

/**
 * @package Prophet
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs;

use DecodeLabs\Exceptional\NotFoundException;
use DecodeLabs\Kingdom\Service;
use DecodeLabs\Kingdom\ServiceTrait;
use DecodeLabs\Prophet\Blueprint;
use DecodeLabs\Prophet\Generator;
use DecodeLabs\Prophet\Model\Assistant;
use DecodeLabs\Prophet\Model\Message;
use DecodeLabs\Prophet\Model\MessageList;
use DecodeLabs\Prophet\Model\Suggestion;
use DecodeLabs\Prophet\Model\Thread;
use DecodeLabs\Prophet\Platform;
use DecodeLabs\Prophet\Repository;
use DecodeLabs\Prophet\Subject;

/**
 * @template A of Assistant
 * @template T of Thread
 * @template S of Suggestion
 * @template J of Subject
 * @template G
 */
class Prophet implements Service
{
    use ServiceTrait;

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

    public function loadPlatform(
        string $name
    ): Platform {
        return $this->slingshot->resolveNamedInstance(Platform::class, $name);
    }

    /**
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
     * @return Blueprint<J>
     */
    public function loadBlueprint(
        string $name
    ): Blueprint {
        $name = Dictum::id($name);
        return $this->slingshot->resolveNamedInstance(Blueprint::class, $name);
    }

    /**
     * @param string|Blueprint<J> $blueprint
     * @return Blueprint<J>
     */
    protected function normalizeBlueprint(
        string|Blueprint $blueprint
    ): Blueprint {
        if (is_string($blueprint)) {
            return $this->loadBlueprint($blueprint);
        }

        return $blueprint;
    }




    /**
     * @return Generator<J,G>
     */
    public function loadGenerator(
        string $name
    ): Generator {
        $output = $this->slingshot->tryResolveNamedInstance(Generator::class, $name);

        if ($output !== null) {
            return $output;
        }

        try {
            $blueprint = $this->normalizeBlueprint($name);

            if ($blueprint instanceof Generator) {
                return $blueprint;
            }
        } catch (NotFoundException $f) {
        }

        throw Exceptional::NotFound(
            message: 'Unable to resolve generator ' . $name
        );
    }

    /**
     * @param J $subject
     * @return G
     */
    public function generate(
        string $name,
        Subject $subject
    ): mixed {
        return $this->loadGenerator($name)->generate($subject);
    }




    /**
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
     * @param string|Blueprint<J> $blueprint
     * @return ($create is true ? A : A|null)
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
     * @param A $assistant
     */
    public function updateAssistant(
        Assistant $assistant
    ): void {
        $platform = $this->loadPlatform($assistant->getServiceName());

        if (!$platform->updateAssistant($assistant)) {
            throw Exceptional::Runtime(
                message: 'Failed to update assistant'
            );
        }

        $this->getRepository()->storeAssistant($assistant);
    }

    /**
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
     * @param string|Blueprint<J> $blueprint
     * @param J $subject
     * @return T|null
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
     * @param string|Blueprint<J> $blueprint
     * @param J $subject
     * @return T
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
     * @param string|Blueprint<J> $blueprint
     * @param J $subject
     * @return ($create is true ? T : T|null)
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
     * @param T $thread
     */
    public function refreshThread(
        Thread $thread
    ): void {
        if ($thread->getServiceId() === null) {
            throw Exceptional::Runtime(
                message: 'Cannot refresh thread that has not completed initialization'
            );
        }

        $platform = $this->loadPlatform($thread->getServiceName());
        $platform->refreshThread($thread);

        $this->getRepository()->storeThread($thread);
    }



    /**
     * @param string|Blueprint<J> $blueprint
     * @param J $subject
     * @return T
     */
    public function loadAndPollThread(
        string|Blueprint $blueprint,
        Subject $subject
    ): Thread {
        $thread = $this->loadThread($blueprint, $subject);
        return $this->pollThread($thread);
    }

    /**
     * @param T $thread
     * @return T
     */
    public function pollThread(
        Thread $thread,
        int $attempts = 5
    ): Thread {
        $count = 0;

        do {
            if ($thread->isReady()) {
                break;
            }

            sleep(3);
            $this->refreshThread($thread);
            continue;
        } while ($count++ < $attempts);

        if (!$thread->isReady()) {
            throw Exceptional::Runtime(
                message: 'Unable to get a response'
            );
        }

        return $thread;
    }



    /**
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
     * @return array<string, mixed>
     */
    public function serializeThreadWithMessages(
        Thread $thread,
        int $limit = 20,
        ?string $afterId = null
    ): array {
        $output = array_merge(
            Coercion::asArray($thread->jsonSerialize()),
            $this->fetchMessages($thread, $limit, $afterId)->jsonSerialize()
        );

        /** @var array<string,mixed> */
        return $output;
    }

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
     * @param T $thread
     */
    public function reply(
        Thread $thread,
        string $message
    ): Message {
        if ($thread->getServiceId() === null) {
            throw Exceptional::Runtime(
                message: 'Cannot reply to thread that has not completed initialization'
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
