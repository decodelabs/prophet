<?php

/**
 * @package Prophet
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Prophet\Model;

/**
 * @phpstan-require-implements Suggestion
 */
trait SuggestionTrait
{
    /**
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
