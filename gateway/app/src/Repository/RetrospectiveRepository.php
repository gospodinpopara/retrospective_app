<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Retrospective;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Retrospective>
 */
class RetrospectiveRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Retrospective::class);
    }

    /**
     * @param int $userId
     * @param int $retrospectiveId
     *
     * @return bool
     */
    public function isRetrospectiveOwner(int $userId, int $retrospectiveId): bool
    {
        $result = $this->createQueryBuilder('r')
            ->select('COUNT(r.id)')
            ->join('r.owner', 'u')
            ->where('r.id = :retrospectiveId')
            ->andWhere('u.id = :userId')
            ->setParameter('retrospectiveId', $retrospectiveId)
            ->setParameter('userId', $userId)
            ->getQuery()
            ->getSingleScalarResult();

        return $result > 0;
    }

    /**
     * @param int $userId
     * @param int $retrospectiveId
     *
     * @return bool
     */
    public function isUserRetrospectiveParticipant(int $userId, int $retrospectiveId): bool
    {
        $result = $this->createQueryBuilder('r')
            ->select('COUNT(rp.id)')
            ->join('r.retrospectiveParticipants', 'rp')
            ->join('rp.user', 'u')
            ->where('r.id = :retrospectiveId')
            ->andWhere('u.id = :userId')
            ->setParameter('retrospectiveId', $retrospectiveId)
            ->setParameter('userId', $userId)
            ->getQuery()
            ->getSingleScalarResult();

        return (int) $result > 0;
    }

    //    /**
    //     * @return Retrospective[] Returns an array of Retrospective objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('r')
    //            ->andWhere('r.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('r.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Retrospective
    //    {
    //        return $this->createQueryBuilder('r')
    //            ->andWhere('r.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
