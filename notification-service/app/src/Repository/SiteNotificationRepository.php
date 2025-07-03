<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\SiteNotification;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<SiteNotification>
 */
class SiteNotificationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SiteNotification::class);
    }

    /**
     * @return SiteNotification[]
     */
    public function getActiveGenericSiteNotifications(): array
    {
        $qb = $this->createQueryBuilder('sn');

        $qb->andWhere('sn.dateFrom <= :now')
            ->andWhere('sn.dateTo >= :now')
            ->andWhere('sn.eolDate >= :now')
            ->andWhere('sn.generic = :generic')
            ->setParameter('now', new \DateTimeImmutable('now'), Types::DATE_IMMUTABLE)
            ->setParameter('generic', true, Types::BOOLEAN);

        return $qb->getQuery()->getResult();
    }

    //    /**
    //     * @return SiteNotification[] Returns an array of SiteNotification objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('s.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?SiteNotification
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
