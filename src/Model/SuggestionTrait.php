<?php

/**
 * @package Prophet
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Prophet\Model;

trait SuggestionTrait
{
    /**
     * Convert to serializable array
     *
     * @return array<string, mixed>
     */
    public function jsonSerialize(): mixed
    {
        return [
            'id' => $this->getId(),
            'action' => $this->getAction(),
            'threadId' => $this->getThreadId(),
            'options' => $this->getOptions(),
            'createdAt' => $this->getCreatedAt(),
            'updatedAt' => $this->getUpdatedAt()
        ];
    }
}
