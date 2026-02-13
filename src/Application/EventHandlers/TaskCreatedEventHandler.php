<?php

namespace App\Application\EventHandlers;

use App\Domain\Events\TaskCreatedEvent;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class TaskCreatedEventHandler
{
    public function __construct(
        private readonly LoggerInterface $logger,
    ) {}

    public function __invoke(TaskCreatedEvent $event): void
    {
        $this->logger->info('Task created event processed', [
            'aggregateId' => $event->getAggregateId(),
            'name' => $event->getName(),
            'assignedUserId' => $event->getAssignedUserId(),
            'occurredAt' => $event->getOccurredAt()->format('c'),
        ]);

        // Add your business logic here:
        // - Send notification emails
        // - Update read models/projections
        // - Trigger external integrations
        // - Update search indexes
    }
}
