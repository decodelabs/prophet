<?php

/**
 * @package Prophet
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Prophet;

use DecodeLabs\Prophet\Model\Assistant;
use DecodeLabs\Prophet\Model\Suggestion;
use DecodeLabs\Prophet\Model\Thread;

/**
 * @template A of Assistant
 * @template T of Thread
 * @template S of Suggestion
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
     * @param A $assistant
     */
    public function deleteAssistant(
        Assistant $assistant
    ): bool;

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

    /**
     * @param T $thread
     */
    public function deleteThread(
        Thread $thread
    ): bool;

    /**
     * @param T $thread
     * @return array<S>
     */
    public function fetchSuggestions(
        Thread $thread
    ): array;

    /**
     * @param T $thread
     * @param array<string> $options
     * @return S
     */
    public function createSuggestion(
        Thread $thread,
        array $options
    ): Suggestion;

    /**
     * @param S $suggestion
     */
    public function storeSuggestion(
        Suggestion $suggestion
    ): void;

    /**
     * @param S $suggestion
     */
    public function deleteSuggestion(
        Suggestion $suggestion
    ): bool;
}
