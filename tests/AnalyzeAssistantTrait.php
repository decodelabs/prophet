<?php

/**
 * @package Prophet
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Prophet\Tests;

use Carbon\Carbon;
use DateTimeInterface;
use DecodeLabs\Prophet\Model\RunStatus;
use DecodeLabs\Prophet\Model\Assistant;
use DecodeLabs\Prophet\Model\AssistantTrait;
use DecodeLabs\Prophet\Service\Medium;

class AnalyzeAssistantTrait implements Assistant
{
    use AssistantTrait;

    public function getId(): ?string
    {
        return null;
    }

    public function getAction(): string
    {
        return 'analyze';
    }

    public function setName(
        string $name
    ): void {
    }

    public function getName(): string
    {
        return '';
    }

    public function setInstructions(
        string $instructions
    ): void {
    }

    public function getInstructions(): string
    {
        return '';
    }

    public function setDescription(
        ?string $description
    ): void {
    }

    public function getDescription(): ?string
    {
        return null;
    }

    public function getMedium(): Medium
    {
        return Medium::Json;
    }

    public function getMetadata(): array
    {
        return [];
    }

    public function getServiceName(): string
    {
        return '';
    }

    public function setServiceId(
        ?string $serviceId
    ): void {
    }

    public function getServiceId(): ?string
    {
        return null;
    }

    public function setLanguageModelName(
        ?string $languageModelName
    ): void {
    }

    public function getLanguageModelName(): ?string
    {
        return null;
    }

    public function setCreatedAt(
        DateTimeInterface $createdAt
    ): void {
    }

    public function getCreatedAt(): Carbon
    {
        return Carbon::now();
    }

    public function setUpdatedAt(
        DateTimeInterface $updatedAt
    ): void {
    }

    public function getUpdatedAt(): Carbon
    {
        return Carbon::now();
    }
}
