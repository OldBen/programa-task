<?php

namespace App\Application\UseCases\Users\Commands;

use App\Domain\Aggregates\UserAggregate;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[AsMessageHandler]
class FetchUserListCommandHandler
{
    public function __construct(
        private HttpClientInterface $httpClient,
        private EntityManagerInterface $entityManager
    ) {
    }

    public function __invoke(FetchUserListCommand $command): void
    {
        $response = $this->httpClient->request('GET', $_ENV['DATASOURCE_URL'] . '/users', [
            'query' => [
                '_limit' => $command->getCount()
            ]
        ]);
        if ($response->getStatusCode() === 200) {
            $users = $response->toArray();
            foreach ($users as $user) {
                //var_dump($user);
                $aggregate = new UserAggregate();
                $aggregate->setName($user['name'])
                    ->setUsername($user['username'])
                    ->setEmail($user['email']);
                $this->entityManager->persist($aggregate);
            }
            $this->entityManager->flush();
        } else {
            throw new \Exception('Failed to fetch user list from external API');
        }
    }
}