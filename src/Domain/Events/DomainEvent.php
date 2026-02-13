<?php

namespace App\Domain\Events;

abstract class DomainEvent
{
    public function __construct(
        private readonly int $aggregateId,
        private readonly \DateTimeImmutable $occurredAt = new \DateTimeImmutable(),
    ) {}

    public function getAggregateId(): int
    {
        return $this->aggregateId;
    }

    public function getOccurredAt(): \DateTimeImmutable
    {
        return $this->occurredAt;
    }
}
