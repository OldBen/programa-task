<?php

namespace App\Infrastructure\EventDispatcher;

use App\Domain\Events\DomainEvent;
use Symfony\Component\Messenger\MessageBusInterface;

final class EventDispatcher
{
    public function __construct(
        private readonly MessageBusInterface $eventBus,
    ) {}

    public function dispatch(DomainEvent $event): void
    {
        $this->eventBus->dispatch($event);
    }

    /**
     * @param DomainEvent[] $events
     */
    public function dispatchMultiple(array $events): void
    {
        foreach ($events as $event) {
            $this->dispatch($event);
        }
    }
}
