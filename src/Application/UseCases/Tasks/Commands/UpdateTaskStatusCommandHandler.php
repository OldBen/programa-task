<?php

namespace App\Application\UseCases\Tasks\Commands;

use App\Infrastructure\Repositories\TaskSourcingRepository;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class UpdateTaskStatusCommandHandler
{
    public function __construct(
        private readonly TaskSourcingRepository $taskRepository,
    ) {}

    public function __invoke(UpdateTaskStatusCommand $command): void
    {
        // Reconstruct the task aggregate from event history
        $task = $this->taskRepository->getById($command->getTaskId());

        if ($task === null) {
            throw new \DomainException(
                sprintf('Task with ID %d not found', $command->getTaskId())
            );
        }

        // Apply the status change (records event)
        $task->changeStatus($command->getNewStatus());

        // Save the aggregate (persists new events and dispatches them)
        $this->taskRepository->save($task);
    }
}
