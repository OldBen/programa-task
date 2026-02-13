<?php

namespace App\Infrastructure\Repositories;

use App\Domain\Aggregates\TaskAggregate;
use App\Domain\Enums\TaskStatusEnum;
use App\Domain\Events\DomainEvent;
use App\Domain\Events\EventStore;
use App\Domain\Events\TaskCreatedEvent;
use App\Domain\Events\TaskReassignedEvent;
use App\Domain\Events\TaskStatusChangedEvent;
use App\Infrastructure\EventDispatcher\EventDispatcher;
use Doctrine\ORM\EntityManagerInterface;

class TaskSourcingRepository
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly EventStoreRepository $eventStoreRepository,
        private readonly EventDispatcher $eventDispatcher,
    ) {}

    public function save(TaskAggregate $aggregate): void
    {
        $uncommittedEvents = $aggregate->getUncommittedEvents();

        foreach ($uncommittedEvents as $event) {
            $eventStore = new EventStore(
                $aggregate->getId(),
                TaskAggregate::class,
                $event,
                $aggregate->getVersion(),
            );
            $this->em->persist($eventStore);
        }

        $this->em->flush();

        // Dispatch events after successful persistence
        $this->eventDispatcher->dispatchMultiple($uncommittedEvents);

        $aggregate->clearUncommittedEvents();
    }

    public function getById(int $id): ?TaskAggregate
    {
        $events = $this->eventStoreRepository->findEventsByAggregateId($id);

        if (empty($events)) {
            return null;
        }

        $reconstructedEvents = array_map(
            fn(EventStore $eventStore) => $this->reconstructEvent($eventStore),
            $events
        );

        return TaskAggregate::fromHistory($reconstructedEvents);
    }

    private function reconstructEvent(EventStore $eventStore): DomainEvent
    {
        $payload = $eventStore->getPayload()['data'];

        return match($eventStore->getEventName()) {
            TaskCreatedEvent::class => new TaskCreatedEvent(
                $eventStore->getAggregateId(),
                $payload['name'],
                $payload['description'],
                $payload['assignedUserId'],
                $eventStore->getCreatedAt(),
            ),
            TaskStatusChangedEvent::class => new TaskStatusChangedEvent(
                $eventStore->getAggregateId(),
                TaskStatusEnum::from($payload['status']),
                $eventStore->getCreatedAt(),
            ),
            TaskReassignedEvent::class => new TaskReassignedEvent(
                $eventStore->getAggregateId(),
                $payload['newAssignedUserId'],
                $eventStore->getCreatedAt(),
            ),
            default => throw new \InvalidArgumentException(
                sprintf('Unknown event: %s', $eventStore->getEventName())
            ),
        };
    }
}
