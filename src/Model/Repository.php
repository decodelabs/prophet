<?php

/**
 * @package Prophet
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Prophet\Model;

use DecodeLabs\Prophet\Blueprint;
use DecodeLabs\Prophet\Subject;

/**
 * @template A of Assistant
 * @template T of Thread
 */
interface Repository
{
    /**
     * @param Blueprint<Subject> $blueprint
     * @return A|null
     */
    public function fetchAssistant(
        Blueprint $blueprint,
        string $serviceName
    ): ?Assistant;

    /**
     * @param Blueprint<Subject> $blueprint
     * @return A
     */
    public function createAssistant(
        Blueprint $blueprint,
        string $serviceName
    ): Assistant;

    /**
     * @param A $assistant
     */
    public function storeAssistant(
        Assistant $assistant
    ): void;

    /**
     * @param Blueprint<Subject> $blueprint
     * @return T|null
     */
    public function fetchThread(
        Blueprint $blueprint,
        Subject $subject
    ): ?Thread;

    /**
     * @param Blueprint<Subject> $blueprint
     * @return T
     */
    public function createThread(
        Blueprint $blueprint,
        Subject $subject
    ): Thread;

    /**
     * @param T $thread
     */
    public function storeThread(
        Thread $thread
    ): void;
}
