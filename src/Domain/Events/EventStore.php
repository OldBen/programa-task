<?php

namespace App\Domain\Events;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\SerializerInterface;

#[ORM\Entity(repositoryClass: EventStoreRepository::class)]
#[ORM\Table(name: 'event_store')]
#[ORM\Index(columns: ['aggregate_id'])]
#[ORM\Index(columns: ['aggregate_id', 'version'])]
#[ORM\Index(columns: ['created_at'])]
class EventStore
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private int $aggregateId;

    #[ORM\Column(length: 255)]
    private string $aggregateType = 'App\Domain\Aggregates\TaskAggregate';

    #[ORM\Column(length: 255)]
    private string $eventName;

    #[ORM\Column(type: 'json')]
    private array $payload;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column]
    private int $version;

    public function __construct(
        int $aggregateId,
        string $aggregateType,
        DomainEvent $event,
        int $version,
    ) {
        $this->aggregateId = $aggregateId;
        $this->aggregateType = $aggregateType;
        $this->eventName = $event::class;
        $this->payload = $this->serializeEvent($event);
        $this->createdAt = $event->getOccurredAt();
        $this->version = $version;
    }

    private function serializeEvent(DomainEvent $event): array
    {
        return match(true) {
            $event instanceof TaskCreatedEvent => [
                'class' => TaskCreatedEvent::class,
                'data' => [
                    'name' => $event->getName(),
                    'description' => $event->getDescription(),
                    'assignedUserId' => $event->getAssignedUserId(),
                ],
            ],
            $event instanceof TaskStatusChangedEvent => [
                'class' => TaskStatusChangedEvent::class,
                'data' => [
                    'status' => $event->getStatus()->value,
                ],
            ],
            $event instanceof TaskReassignedEvent => [
                'class' => TaskReassignedEvent::class,
                'data' => [
                    'newAssignedUserId' => $event->getNewAssignedUserId(),
                ],
            ],
            default => throw new \InvalidArgumentException(sprintf('Unknown event type: %s', $event::class)),
        };
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAggregateId(): int
    {
        return $this->aggregateId;
    }

    public function getAggregateType(): string
    {
        return $this->aggregateType;
    }

    public function getEventName(): string
    {
        return $this->eventName;
    }

    public function getPayload(): array
    {
        return $this->payload;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getVersion(): int
    {
        return $this->version;
    }
}
