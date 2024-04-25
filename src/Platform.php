<?php

/**
 * @package Prophet
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Prophet;

use DecodeLabs\Prophet\Model\Assistant;
use DecodeLabs\Prophet\Model\Thread;
use DecodeLabs\Prophet\Service\Feature;
use DecodeLabs\Prophet\Service\LanguageModelLevel;
use DecodeLabs\Prophet\Service\Medium;

interface Platform
{
    public function getName(): string;

    public function supportsMedium(
        Medium $medium
    ): bool;

    public function supportsFeature(
        Medium $medium,
        Feature $feature
    ): bool;

    /**
     * @param array<Feature> $features
     */
    public function suggestModel(
        Medium $medium,
        LanguageModelLevel $level = LanguageModelLevel::Standard,
        array $features = []
    ): string;

    /**
     * Find assistant by action
     */
    public function findAssistant(
        Assistant $assistant
    ): bool;

    /**
     * Create assistant object if supported
     */
    public function createAssistant(
        Assistant $assistant
    ): void;

    /**
     * Start thread
     */
    public function startThread(
        Assistant $assistant,
        Thread $thread,
        ?string $additionalInstructions = null
    ): void;

    /**
     * Refresh thread
     */
    public function refreshThread(
        Thread $thread
    ): void;


    /**
     * Fetch messages
     *
     * @return array<string, mixed>
     */
    public function fetchMessages(
        Thread $thread,
        ?string $afterId = null,
        int $limit = 20
    ): array;

    /**
     * Send reply
     *
     * @return array<string, mixed>
     */
    public function reply(
        Assistant $assistant,
        Thread $thread,
        string $message
    ): array;
}
