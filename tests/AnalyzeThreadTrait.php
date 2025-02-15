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
use DecodeLabs\Prophet\Model\Thread;
use DecodeLabs\Prophet\Model\ThreadTrait;
use DecodeLabs\Prophet\Service\Medium;

class AnalyzeThreadTrait implements Thread
{
    use ThreadTrait;

    public function getId(): ?string
    {
        return null;
    }

    public function getAction(): string
    {
        return 'test';
    }

    public function getOwnerId(): ?string
    {
        return null;
    }

    public function getSubjectType(): string
    {
        return 'test';
    }

    public function getSubjectId(): ?string
    {
        return null;
    }

    public function getMedium(): Medium
    {
        return Medium::Text;
    }

    public function getServiceName(): string
    {
        return 'test';
    }

    public function setServiceId(
        ?string $serviceId
    ): void {
    }

    public function getServiceId(): ?string
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

    public function setStartedAt(
        ?DateTimeInterface $startedAt
    ): void {
    }

    public function getStartedAt(): ?Carbon
    {
        return null;
    }

    public function setCompletedAt(
        ?DateTimeInterface $completedAt
    ): void {
    }

    public function getCompletedAt(): ?Carbon
    {
        return null;
    }

    public function setExpiresAt(
        ?DateTimeInterface $expiresAt
    ): void {
    }

    public function getExpiresAt(): ?Carbon
    {
        return null;
    }

    public function setRunId(
        ?string $runId
    ): void {
    }

    public function getRunId(): ?string
    {
        return null;
    }

    public function isReady(): bool
    {
        return false;
    }

    public function setRawStatus(
        ?string $status
    ): void {
    }

    public function getRawStatus(): ?string
    {
        return null;
    }

    public function setStatus(
        ?RunStatus $status
    ): void {
    }

    public function getStatus(): ?RunStatus
    {
        return null;
    }
}
