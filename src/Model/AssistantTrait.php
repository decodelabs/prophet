<?php

/**
 * @package Prophet
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Prophet\Model;

use DecodeLabs\Prophet;
use DecodeLabs\Prophet\Service\Medium;

/**
 * @phpstan-require-implements Assistant
 */
trait AssistantTrait
{
    /**
     * Get medim
     */
    public function getMedium(): Medium
    {
        $blueprint = Prophet::loadBlueprint($this->getAction());
        return $blueprint->getMedium();
    }

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
