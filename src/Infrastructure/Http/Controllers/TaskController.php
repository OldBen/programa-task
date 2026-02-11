<?php

namespace App\Infrastructure\Http\Controllers;

use App\Domain\Aggregates\TaskAggregate;
use App\Domain\Aggregates\UserAggregate;
use App\Domain\Enums\TaskStatusEnum;
use App\Domain\Events\TaskCreatedEvent;
use App\Domain\Events\TaskStatusUpdatedEvent;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Messenger\MessageBusInterface;

final class TaskController extends AbstractController
{
    #[Route('/tasks', name: 'app_task', methods: ['GET'])]
    public function index(EntityManagerInterface $em): Response
    {
        $tasks = $em->getRepository(TaskAggregate::class)->findAll();
        return $this->json($tasks);
    }

    #[Route('/tasks', name: 'app_task_create', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $em): Response
    {
        $data = json_decode($request->getContent(), true);
        $task = new TaskAggregate();
        $task->setName($data['name'])
            ->setDescription($data['description'])
            ->setStatus(TaskStatusEnum::TODO)
            ->setAssignedUser($em->getReference(UserAggregate::class, $data['assigned_user_id']));
        $em->persist($task);
        $em->flush();
        return $this->json($task, Response::HTTP_CREATED);
    }

    #[Route('/tasks/{id}', name: 'app_task_update', methods: ['PUT'])]
    public function update(int $id, Request $request, EntityManagerInterface $em, MessageBusInterface $messageBus): Response
    {
        $data = json_decode($request->getContent(), true);
        $task = $em->getReference(TaskAggregate::class, $id);
        $task->setStatus(TaskStatusEnum::fromString($data['status']));
        $em->flush();
        $messageBus->dispatch(new TaskStatusUpdatedEvent($id, $data['status']));
        return $this->noContent();
    }
}
