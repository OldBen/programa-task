<?php

namespace App\Domain\Events;

use App\Domain\Enums\TaskStatusEnum;

class TaskStatusChangedEvent extends DomainEvent
{
    public function __construct(
        int $aggregateId,
        private readonly TaskStatusEnum $status,
        \DateTimeImmutable $occurredAt = new \DateTimeImmutable(),
    ) {
        parent::__construct($aggregateId, $occurredAt);
    }

    public function getStatus(): TaskStatusEnum
    {
        return $this->status;
    }
}