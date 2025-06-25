<?php

declare(strict_types=1);

namespace App\Repository;

use App\DTO\Filter\ParticipantFilter;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @extends ServiceEntityRepository<User>
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(\sprintf('Instances of "%s" are not supported.', $user::class));
        }

        $user->setPassword($newHashedPassword);
        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();
    }

    public function existsByEmail(string $email): bool
    {
        return (bool) $this->createQueryBuilder('u')
            ->select('COUNT(u.id)')
            ->where('u.email = :email')
            ->setParameter('email', $email)
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * @param ParticipantFilter $filters
     * @param User              $user
     *
     * @return array
     */
    public function searchParticipants(ParticipantFilter $filters, User $user): array
    {
        $queryBuilder = $this->createQueryBuilder('u');

        $queryBuilder->andWhere('u.id != :currentUserId')
            ->setParameter('currentUserId', $user->getId());

        if ($filters->getEmail()) {
            $queryBuilder->andWhere('u.email LIKE :email')
                ->setParameter('email', '%'.$filters->getEmail().'%');
        }

        if ($filters->getFirstName()) {
            $queryBuilder->andWhere('u.firstName LIKE :firstName')
                ->setParameter('firstName', '%'.$filters->getFirstName().'%');
        }

        if ($filters->getLastName()) {
            $queryBuilder->andWhere('u.lastName LIKE :lastName')
                ->setParameter('lastName', '%'.$filters->getLastName().'%');
        }

        $currentPage = $filters->getPage();
        $itemsPerPage = $filters->getItemsPerPage();

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
    //     * @return User[] Returns an array of User objects
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

    //    public function findOneBySomeField($value): ?User
    //    {
    //        return $this->createQueryBuilder('u')
    //            ->andWhere('u.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
