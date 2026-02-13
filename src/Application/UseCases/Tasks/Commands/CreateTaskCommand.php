<?php

namespace App\Application\UseCases\Tasks\Commands;

final class CreateTaskCommand
{
    public function __construct(
        private readonly int $taskId,
        private readonly string $name,
        private readonly ?string $description,
        private readonly int $assignedUserId,
    ) {}

    public function getTaskId(): int
    {
        return $this->taskId;
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
