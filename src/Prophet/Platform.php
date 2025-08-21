<?php

/**
 * @package Prophet
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Prophet;

use DecodeLabs\Prophet\Model\Assistant;
use DecodeLabs\Prophet\Model\Message;
use DecodeLabs\Prophet\Model\MessageList;
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
     * @param array<Feature> $features
     */
    public function shouldUpdateModel(
        string $oldModel,
        string $newModel,
        Medium $medium,
        LanguageModelLevel $level = LanguageModelLevel::Standard,
        array $features = []
    ): bool;

    public function findAssistant(
        Assistant $assistant
    ): bool;

    public function createAssistant(
        Assistant $assistant
    ): void;

    public function updateAssistant(
        Assistant $assistant
    ): bool;

    public function deleteAssistant(
        Assistant $assistant
    ): bool;

    public function startThread(
        Assistant $assistant,
        Thread $thread,
        ?string $additionalInstructions = null
    ): void;

    public function refreshThread(
        Thread $thread
    ): void;

    public function deleteThread(
        Thread $thread
    ): bool;

    public function fetchMessages(
        Thread $thread,
        int $limit = 20,
        string|int|null $after = null
    ): MessageList;

    public function reply(
        Assistant $assistant,
        Thread $thread,
        string $message
    ): Message;
}
