<?php

declare(strict_types=1);

namespace App\Repository;

use App\DTO\Filter\RetrospectiveInvitesFilter;
use App\Entity\RetrospectiveParticipant;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<RetrospectiveParticipant>
 */
class RetrospectiveParticipantRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RetrospectiveParticipant::class);
    }

    public function getUserRetrospectiveInvites(User $user, RetrospectiveInvitesFilter $filter): array
    {
        $queryBuilder = $this->createQueryBuilder('rp')
            ->leftJoin('rp.retrospective', 'r');

        $queryBuilder->andWhere('rp.user = :userId')
            ->setParameter('userId', $user->getId());

        if ($filter->getStatus()) {
            $queryBuilder->andWhere('rp.status = :status')
                ->setParameter('status', $filter->getStatus());
        }

        if ($filter->getOrderByRetrospectiveStartTime()) {
            $queryBuilder->orderBy('r.startTime', $filter->getOrderByRetrospectiveStartTime());
        } else {
            $queryBuilder->orderBy('r.startTime', 'DESC');
        }

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
    //     * @return RetrospectiveParticipant[] Returns an array of RetrospectiveParticipant objects
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

    //    public function findOneBySomeField($value): ?RetrospectiveParticipant
    //    {
    //        return $this->createQueryBuilder('r')
    //            ->andWhere('r.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
