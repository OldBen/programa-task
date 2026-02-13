<?php

namespace App\Domain\Events;

class TaskCreatedEvent extends DomainEvent
{
    public function __construct(
        int $aggregateId,
        private readonly string $name,
        private readonly ?string $description,
        private readonly int $assignedUserId,
        \DateTimeImmutable $occurredAt = new \DateTimeImmutable(),
    ) {
        parent::__construct($aggregateId, $occurredAt);
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getAssignedUserId(): int
    {
        return $this->assignedUserId;
    }
}