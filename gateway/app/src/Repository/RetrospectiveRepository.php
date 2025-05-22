<?php

declare(strict_types=1);

namespace App\Repository;

use App\DTO\Filter\RetrospectiveFilter;
use App\Entity\Retrospective;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
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
    public function isUserRetrospectiveOwner(int $userId, int $retrospectiveId): bool
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

    /**
     * @param RetrospectiveFilter $filter
     * @param int                 $userId
     *
     * @return array
     */
    public function getUserRetrospectives(RetrospectiveFilter $filter, int $userId): array
    {
        $queryBuilder = $this->createQueryBuilder('r')
            ->leftJoin('r.retrospectiveParticipants', 'rp')
            ->leftJoin('rp.user', 'participant');

        if ($filter->getIsOwner() === true) {
            $queryBuilder->andWhere('r.owner = :userId');
        } elseif ($filter->getIsOwner() === false) {
            $queryBuilder->andWhere('participant.id = :userId');
        } else {
            $queryBuilder->andWhere('r.owner = :userId OR participant.id = :userId');
        }

        $queryBuilder->setParameter('userId', $userId);

        $currentPage = $filter->getPage();
        $itemsPerPage = $filter->getItemsPerPage();

        $queryBuilder
            ->setFirstResult(($currentPage - 1) * $itemsPerPage)
            ->setMaxResults($itemsPerPage);

        $doctrinePaginator = new Paginator($queryBuilder->getQuery(), true);

        $totalItems = \count($doctrinePaginator);
        $paginatedResults = [];
        foreach ($doctrinePaginator as $result) {
            $paginatedResults[] = $result;
        }

        return [
            'items' => $paginatedResults,
            'currentPage' => $currentPage,
            'itemsPerPage' => $itemsPerPage,
            'totalItems' => $totalItems,
            'totalPages' => (int) ceil($totalItems / $itemsPerPage),
        ];
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
