<?php

namespace App\Infrastructure\Repositories;

use App\Domain\Events\EventStore;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<EventStore>
 */
class EventStoreRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EventStore::class);
    }

    public function findEventsByAggregateId(int $aggregateId): array
    {
        return $this->findBy(
            ['aggregateId' => $aggregateId],
            ['version' => 'ASC']
        );
    }

    public function findEventsByAggregateIdAfterId(int $aggregateId, int $afterVersion): array
    {
        return $this->createQueryBuilder('e')
            ->where('e.aggregateId = :aggregateId')
            ->andWhere('e.version > :afterVersion')
            ->setParameter('aggregateId', $aggregateId)
            ->setParameter('afterVersion', $afterVersion)
            ->orderBy('e.version', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function getLatestVersion(int $aggregateId): int
    {
        $result = $this->createQueryBuilder('e')
            ->select('MAX(e.version) as maxVersion')
            ->where('e.aggregateId = :aggregateId')
            ->setParameter('aggregateId', $aggregateId)
            ->getQuery()
            ->getOneOrNullResult();

        return (int) ($result['maxVersion'] ?? 0);
    }
}
