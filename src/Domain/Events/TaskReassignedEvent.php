<?php

namespace App\Domain\Events;

class TaskReassignedEvent extends DomainEvent
{
    public function __construct(
        int $aggregateId,
        private readonly int $newAssignedUserId,
        \DateTimeImmutable $occurredAt = new \DateTimeImmutable(),
    ) {
        parent::__construct($aggregateId, $occurredAt);
    }

    public function getNewAssignedUserId(): int
    {
        return $this->newAssignedUserId;
    }
}