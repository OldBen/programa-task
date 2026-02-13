<?php

namespace App\Application\EventHandlers;

use App\Domain\Events\TaskStatusChangedEvent;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class TaskStatusChangedEventHandler
{
    public function __construct(
        private readonly LoggerInterface $logger,
    ) {}

    public function __invoke(TaskStatusChangedEvent $event): void
    {
        $this->logger->info('Task status changed event processed', [
            'aggregateId' => $event->getAggregateId(),
            'newStatus' => $event->getStatus()->value,
            'occurredAt' => $event->getOccurredAt()->format('c'),
        ]);

        // Add your business logic here:
        // - Send status update notifications
        // - Update dashboards/metrics
        // - Trigger workflow transitions
        // - Update read models
    }
}
