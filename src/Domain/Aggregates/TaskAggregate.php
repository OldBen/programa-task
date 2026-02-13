<?php

namespace App\Domain\Aggregates;

use App\Domain\Enums\TaskStatusEnum;
use App\Domain\Events\DomainEvent;
use App\Domain\Events\TaskCreatedEvent;
use App\Domain\Events\TaskReassignedEvent;
use App\Domain\Events\TaskStatusChangedEvent;

class TaskAggregate
{
    private int $id;
    private string $name;
    private ?string $description;
    private TaskStatusEnum $status;
    private int $assignedUserId;
    private int $version = 0;
    private array $uncommittedEvents = [];

    private function __construct() {}

    public static function create(
        int $id,
        string $name,
        ?string $description,
        int $assignedUserId,
    ): self {
        $aggregate = new self();
        $aggregate->id = $id;

        $event = new TaskCreatedEvent(
            $id,
            $name,
            $description,
            $assignedUserId,
        );

        $aggregate->recordEvent($event);
        $aggregate->applyEvent($event);

        return $aggregate;
    }

    public static function fromHistory(array $events): self
    {
        $aggregate = new self();

        foreach ($events as $event) {
            $aggregate->applyEvent($event);
        }

        return $aggregate;
    }

    public function changeStatus(TaskStatusEnum $newStatus): void
    {
        if ($this->status === $newStatus) {
            return;
        }

        $event = new TaskStatusChangedEvent(
            $this->id,
            $newStatus,
        );
        $this->recordEvent($event);
        $this->applyEvent($event);
    }

    public function reassignTo(int $newAssignedUserId): void
    {
        if ($this->assignedUserId === $newAssignedUserId) {
            return;
        }

        $event = new TaskReassignedEvent(
            $this->id,
            $newAssignedUserId,
        );
        $this->recordEvent($event);
        $this->applyEvent($event);
    }

    private function applyEvent(DomainEvent $event): void
    {
        match($event::class) {
            TaskCreatedEvent::class => $this->applyTaskCreated($event),
            TaskStatusChangedEvent::class => $this->applyStatusChanged($event),
            TaskReassignedEvent::class => $this->applyReassigned($event),
        };
        $this->version++;
    }

    private function applyTaskCreated(TaskCreatedEvent $event): void
    {
        $this->id = $event->getAggregateId();
        $this->name = $event->getName();
        $this->description = $event->getDescription();
        $this->assignedUserId = $event->getAssignedUserId();
        $this->status = TaskStatusEnum::TODO;
    }

    private function applyStatusChanged(TaskStatusChangedEvent $event): void
    {
        $this->status = $event->getStatus();
    }

    private function applyReassigned(TaskReassignedEvent $event): void
    {
        $this->assignedUserId = $event->getNewAssignedUserId();
    }

    private function recordEvent(DomainEvent $event): void
    {
        $this->uncommittedEvents[] = $event;
    }

    public function getUncommittedEvents(): array
    {
        return $this->uncommittedEvents;
    }

    public function clearUncommittedEvents(): void
    {
        $this->uncommittedEvents = [];
    }

    public function getVersion(): int
    {
        return $this->version;
    }

    // Getters
    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getStatus(): TaskStatusEnum
    {
        return $this->status;
    }

    public function getAssignedUserId(): int
    {
        return $this->assignedUserId;
    }
}
