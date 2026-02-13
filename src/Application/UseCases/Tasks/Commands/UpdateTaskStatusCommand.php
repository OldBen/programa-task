<?php

namespace App\Application\UseCases\Tasks\Commands;

use App\Domain\Enums\TaskStatusEnum;

final class UpdateTaskStatusCommand
{
    public function __construct(
        private readonly int $taskId,
        private readonly TaskStatusEnum $newStatus,
    ) {}

    public function getTaskId(): int
    {
        return $this->taskId;
    }

    public function getNewStatus(): TaskStatusEnum
    {
        return $this->newStatus;
    }
}
