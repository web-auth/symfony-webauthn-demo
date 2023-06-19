<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Psr\Clock\ClockInterface;

/**
 * @extends ServiceEntityRepository<User>
 *
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
final class UserRepository extends ServiceEntityRepository
{
    public function __construct(
        ManagerRegistry $registry,
        private readonly ClockInterface $clock
    ) {
        parent::__construct($registry, User::class);
    }

    public function save(User $user): void
    {
        $this->getEntityManager()
            ->persist($user)
        ;
        $this->getEntityManager()
            ->flush()
        ;
    }

    /**
     * @return iterable<User>
     */
    public function findAllInactive(): iterable
    {
        $twoWeeksAgo = $this->clock->now()
            ->modify('-2 weeks');
        return $this->createQueryBuilder('u')
            ->andWhere('u.lastLoginAt < :date')
            ->setParameter('date', $twoWeeksAgo)
            ->getQuery()
            ->getResult()
        ;
    }

    public function remove(User $user): void
    {
        $this->getEntityManager()
            ->remove($user);
        $this->getEntityManager()
            ->flush();
    }
}
