<?php

namespace App\Application\UseCases\Tasks\Commands;

use App\Domain\Aggregates\TaskAggregate;
use App\Infrastructure\Repositories\TaskSourcingRepository;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class CreateTaskCommandHandler
{
    public function __construct(
        private readonly TaskSourcingRepository $taskRepository,
    ) {}

    public function __invoke(CreateTaskCommand $command): void
    {
        // Create new task aggregate using event sourcing
        $task = TaskAggregate::create(
            $command->getTaskId(),
            $command->getName(),
            $command->getDescription(),
            $command->getAssignedUserId(),
        );

        // Save the aggregate (persists events and dispatches them)
        $this->taskRepository->save($task);
    }
}
