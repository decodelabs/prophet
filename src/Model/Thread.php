<?php

/**
 * @package Prophet
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Prophet\Model;

use Carbon\Carbon;
use DateTimeInterface;
use JsonSerializable;

interface Thread extends JsonSerializable
{
    public function getId(): ?string;
    public function getAction(): string;
    public function getOwnerId(): ?string;

    public function getSubjectType(): string;
    public function getSubjectId(): ?string;

    public function getServiceName(): string;

    public function setServiceId(
        ?string $serviceId
    ): void;

    public function getServiceId(): ?string;


    public function setCreatedAt(
        DateTimeInterface $createdAt
    ): void;

    public function getCreatedAt(): Carbon;

    public function setUpdatedAt(
        DateTimeInterface $updatedAt
    ): void;

    public function getUpdatedAt(): Carbon;

    public function setStartedAt(
        ?DateTimeInterface $startedAt
    ): void;

    public function getStartedAt(): ?Carbon;

    public function setCompletedAt(
        ?DateTimeInterface $completedAt
    ): void;

    public function getCompletedAt(): ?Carbon;

    public function setExpiresAt(
        ?DateTimeInterface $expiresAt
    ): void;

    public function getExpiresAt(): ?Carbon;

    public function setRunId(
        ?string $runId
    ): void;

    public function getRunId(): ?string;

    public function isReady(): bool;

    public function setRawStatus(
        ?string $status
    ): void;

    public function getRawStatus(): ?string;

    public function setStatus(
        ?RunStatus $status
    ): void;

    public function getStatus(): ?RunStatus;
}
