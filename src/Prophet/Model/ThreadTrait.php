<?php

/**
 * @package Prophet
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Prophet\Model;

use DecodeLabs\Monarch;
use DecodeLabs\Prophet;
use DecodeLabs\Prophet\Service\Medium;

/**
 * @phpstan-require-implements Thread
 */
trait ThreadTrait
{
    public function getMedium(): Medium
    {
        $prophet = Monarch::getService(Prophet::class);
        $blueprint = $prophet->loadBlueprint($this->getAction());
        return $blueprint->getMedium();
    }

    public function isReady(): bool
    {
        return
            $this->getCompletedAt() !== null &&
            match ($this->getStatus()) {
                RunStatus::Queued,
                RunStatus::InProgress,
                RunStatus::RequiresAction => false,
                default => true
            };
    }

    /**
     * @return array<string, mixed>
     */
    public function jsonSerialize(): mixed
    {
        return [
            'id' => $this->getId(),
            'action' => $this->getAction(),
            'ownerId' => $this->getOwnerId(),
            'subjectType' => $this->getSubjectType(),
            'subjectId' => $this->getSubjectId(),
            'serviceName' => $this->getServiceName(),
            'serviceId' => $this->getServiceName(),
            'createdAt' => $this->getCreatedAt(),
            'updatedAt' => $this->getUpdatedAt(),
            'startedAt' => $this->getStartedAt(),
            'completedAt' => $this->getCompletedAt(),
            'expiresAt' => $this->getExpiresAt(),
            'runId' => $this->getRunId(),
            'rawStatus' => $this->getRawStatus(),
            'status' => $this->getStatus()?->value
        ];
    }
}
