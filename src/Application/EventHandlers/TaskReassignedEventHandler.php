<?php

namespace App\Application\EventHandlers;

use App\Domain\Events\TaskReassignedEvent;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class TaskReassignedEventHandler
{
    public function __construct(
        private readonly LoggerInterface $logger,
    ) {}

    public function __invoke(TaskReassignedEvent $event): void
    {
        $this->logger->info('Task reassigned event processed', [
            'aggregateId' => $event->getAggregateId(),
            'newAssignedUserId' => $event->getNewAssignedUserId(),
            'occurredAt' => $event->getOccurredAt()->format('c'),
        ]);

        // Add your business logic here:
        // - Notify old assignee
        // - Notify new assignee
        // - Update user task lists
        // - Update analytics
    }
}
