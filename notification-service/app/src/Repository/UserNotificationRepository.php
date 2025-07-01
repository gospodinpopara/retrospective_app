<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\UserNotification;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<UserNotification>
 */
class UserNotificationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserNotification::class);
    }

    public function getLatestActiveUserNotifications(int $userId, ?int $limit = null): array
    {
        $qb = $this->createQueryBuilder('un')
            ->join('un.notification', 'n')
            ->andWhere('n.dateFrom <= :now')
            ->andWhere('n.dateTo >= :now')
            ->andWhere('n.eolDate >= :now')
            ->andWhere('un.userId = :userId')
            ->addOrderBy('n.dateFrom', 'DESC')
            ->addOrderBy('n.eolDate', 'DESC')
            ->setParameter('now', new \DateTimeImmutable(), Types::DATETIME_MUTABLE)
            ->setParameter('userId', $userId, Types::INTEGER);

        if ($limit !== null) {
            $qb->setMaxResults($limit);
        }

        return $qb->getQuery()->getResult();
    }

    public function getNotVisitedCount(int $userId): int
    {
        return (int) $this->createQueryBuilder('un')
            ->select('count(n.id)')
            ->join('un.notification', 'n')
            ->andWhere('un.visited = :visited')
            ->andWhere('n.dateFrom <= :now')
            ->andWhere('n.dateTo >= :now')
            ->andWhere('un.userId = :userId')
            ->setParameter('now', new \DateTimeImmutable(), Types::DATETIME_IMMUTABLE)
            ->setParameter('visited', false, Types::BOOLEAN)
            ->setParameter('userId', $userId, Types::INTEGER)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function getNotAckedCount(int $userId): int
    {
        return (int) $this->createQueryBuilder('un')
            ->select('count(un.id)')
            ->join('un.notification', 'n')
            ->andWhere('un.ack = :ack')
            ->andWhere('un.userId = :userId')
            ->andWhere('n.dateFrom <= :now')
            ->andWhere('n.dateTo >= :now')
            ->setParameter('ack', false, Types::BOOLEAN)
            ->setParameter('userId', $userId, Types::INTEGER)
            ->setParameter('now', new \DateTimeImmutable(), Types::DATETIME_IMMUTABLE)
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * @return UserNotification[]
     */
    public function getUserActiveGenericNotifications(): array
    {
        return $this->createQueryBuilder('un')
            ->join('un.notification', 'n')
            ->andWhere('n.generic = :generic')
            ->andWhere('n.dateFrom <= :now')
            ->andWhere('n.dateTo >= :now')
            ->andWhere('n.eolDate >= :now')
            ->setParameter('generic', true, Types::BOOLEAN)
            ->setParameter('now', new \DateTimeImmutable(), Types::DATETIME_IMMUTABLE)
            ->getQuery()
            ->getResult();
    }

    public function setAllAsServed(int $userId): bool
    {
        return $this->createQueryBuilder('un')
            ->update()
            ->set('un.served', ':served')
            ->where('un.userId = :userId')
            ->setParameter('served', true, Types::BOOLEAN)
            ->setParameter('userId', $userId, Types::INTEGER)
            ->getQuery()
            ->getResult();
    }

    //    /**
    //     * @return UserNotification[] Returns an array of UserNotification objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('u')
    //            ->andWhere('u.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('u.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?UserNotification
    //    {
    //        return $this->createQueryBuilder('u')
    //            ->andWhere('u.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
