<?php

/**
 * @package Prophet
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Prophet\Model;

use Carbon\Carbon;
use DateTimeInterface;
use DecodeLabs\Prophet\Service\Medium;
use JsonSerializable;

interface Assistant extends JsonSerializable
{
    public function getId(): ?string;
    public function getAction(): string;

    public function setName(
        string $name
    ): void;

    public function getName(): string;

    public function setInstructions(
        string $instructions
    ): void;

    public function getInstructions(): string;

    public function setDescription(
        ?string $description
    ): void;

    public function getDescription(): ?string;

    public function getMedium(): Medium;

    /**
     * @return array<string, mixed>
     */
    public function getMetadata(): array;

    public function getServiceName(): string;

    public function setServiceId(
        ?string $serviceId
    ): void;

    public function getServiceId(): ?string;

    public function setLanguageModelName(
        ?string $languageModelName
    ): void;

    public function getLanguageModelName(): ?string;

    public function setCreatedAt(
        DateTimeInterface $createdAt
    ): void;

    public function getCreatedAt(): Carbon;

    public function setUpdatedAt(
        DateTimeInterface $updatedAt
    ): void;

    public function getUpdatedAt(): Carbon;
}
