<?php

declare(strict_types=1);

/*
 * This file is part of the Webauthn Demo project.
 *
 * (c) Florent Morselli
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepositoryInterface;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;
use LogicException;

final class UserRepository implements ServiceEntityRepositoryInterface
{
    /**
     * @var EntityManagerInterface
     */
    private $manager;

    public function __construct(ManagerRegistry $registry)
    {
        $manager = $registry->getManagerForClass(User::class);

        if (null === $manager) {
            throw new LogicException(sprintf(
                'Could not find the entity manager for class "%s". Check your Doctrine configuration to make sure it is configured to load this entity’s metadata.',
                User::class
            ));
        }

        $this->manager = $manager;
    }

    public function save(User $user): void
    {
        $this->manager->persist($user);
        $this->manager->flush();
    }

    public function findOneByUserHandle(string $userHandle): ?User
    {
        $qb = $this->manager->createQueryBuilder();

        return $qb->select('u')
            ->from(User::class, 'u')
            ->where('u.user_handle = :user_handle')
            ->setParameter(':user_handle', $userHandle)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult()
            ;
    }

    public function find(string $username): ?User
    {
        $qb = $this->manager->createQueryBuilder();

        return $qb->select('u')
            ->from(User::class, 'u')
            ->where('u.name = :name')
            ->setParameter(':name', $username)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function findOneById(string $id): ?User
    {
        $qb = $this->manager->createQueryBuilder();

        return $qb->select('u')
            ->from(User::class, 'u')
            ->where('u.id = :id')
            ->setParameter(':id', $id)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
}
