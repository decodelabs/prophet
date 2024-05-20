<?php

/**
 * @package Prophet
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Prophet\Model;

trait AssistantTrait
{
    /**
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        return [
            'id' => $this->getId(),
            'name' => $this->getName(),
            'action' => $this->getAction(),
            'instructions' => $this->getInstructions(),
            'description' => $this->getDescription(),
            'languageModelName' => $this->getLanguageModelName(),
            'metadata' => $this->getMetadata(),
            'serviceId' => $this->getServiceId(),
            'createdAt' => $this->getCreatedAt(),
            'updatedAt' => $this->getUpdatedAt()
        ];
    }
}
