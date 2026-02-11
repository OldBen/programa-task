<?php

namespace App\Infrastructure\Http\Controllers;

use App\Domain\Aggregates\TaskAggregate;
use App\Domain\Aggregates\UserAggregate;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class UserController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $em,
    ) {
    }

    #[Route('/users', name: 'app_user', methods: ['GET'])]
    public function index(): Response
    {
        $users = $this->em->getRepository(UserAggregate::class)->findAll();
        return $this->json($users);
    }

    #[Route('/users/{id}/tasks', name: 'app_user_tasks', methods: ['GET'])]
    public function getUserTasks(int $id): Response
    {
        $user = $this->em->getReference(UserAggregate::class, $id);
        $tasks = $this->em->getRepository(TaskAggregate::class)->findBy(['assignedUser' => $user]);
        return $this->json($tasks);
    }

}
