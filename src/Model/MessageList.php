<?php

/**
 * @package Prophet
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Prophet\Model;

use JsonSerializable;

class MessageList implements JsonSerializable
{
    protected bool $hasMore = false;
    protected string|int|null $last = null;

    /**
     * @var array<int, Message>
     */
    protected array $messages = [];

    public function __construct(
        bool $hasMore = false,
        string|int|null $last = null
    ) {
        $this->hasMore = $hasMore;
        $this->last = $last;
    }

    public function hasMore(): bool
    {
        return $this->hasMore;
    }

    public function getLast(): string|int|null
    {
        return $this->last;
    }

    public function addMessage(
        Message $message
    ): void {
        $this->messages[] = $message;
    }

    /**
     * @return array<int, Message>
     */
    public function getAllMessages(): array
    {
        return $this->messages;
    }

    public function getMessage(
        int $index
    ): ?Message {
        return $this->messages[$index] ?? null;
    }

    /**
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        return [
            'hasMore' => $this->hasMore,
            'last' => $this->last,
            'messages' => $this->messages
        ];
    }
}
