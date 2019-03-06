<?php

declare(strict_types=1);

/*
 * This file is part of the appname project.
 *
 * (c) Romain Gautier <mail@romain.sh>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Repository;

use App\Entity\Credential;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepositoryInterface;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;
use LogicException;
use Webauthn\AttestedCredentialData;
use Webauthn\CredentialRepository as CredentialRepositoryInterface;

final class CredentialRepository implements CredentialRepositoryInterface, ServiceEntityRepositoryInterface
{
    /**
     * @var EntityManagerInterface
     */
    private $manager;

    public function __construct(ManagerRegistry $registry)
    {
        $manager = $registry->getManagerForClass(Credential::class);

        if (null === $manager) {
            throw new LogicException(sprintf(
                'Could not find the entity manager for class "%s". Check your Doctrine configuration to make sure it is configured to load this entity’s metadata.',
                Credential::class
            ));
        }

        $this->manager = $manager;
    }

    public function save(Credential $credential): void
    {
        $this->manager->persist($credential);
        $this->manager->flush();
    }

    public function allFromTheList(array $list): array
    {
        $qb = $this->manager->createQueryBuilder();

        return $qb->select('c')
            ->from(Credential::class, 'c')
            ->where('c.credential_id IN (:ids)')
            ->setParameter(':ids', $list)
            ->getQuery()
            ->execute()
        ;
    }

    public function all(): iterable
    {
        $qb = $this->manager->createQueryBuilder();

        return $qb->select('c')
            ->from(Credential::class, 'c')
            ->getQuery()
            ->execute()
        ;
    }

    public function findCredential(string $credentialId): ?Credential
    {
        $qb = $this->manager->createQueryBuilder();

        return $qb->select('c')
            ->from(Credential::class, 'c')
            ->where('c.credential_id = :credential_id')
            ->setParameter(':credential_id', $credentialId)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function has(string $credentialId): bool
    {
        return null !== $this->findCredential($credentialId);
    }

    public function get(string $credentialId): AttestedCredentialData
    {
        $credential = $this->findCredential($credentialId);
        if (!$credential instanceof Credential) {
            throw new \InvalidArgumentException('Not found');
        }

        return $credential->getAttestedCredentialData();
    }

    public function getUserHandleFor(string $credentialId): string
    {
        $credential = $this->findCredential($credentialId);
        if (!$credential instanceof Credential) {
            throw new \InvalidArgumentException('Not found');
        }

        return $credential->getUser()->getId();
    }

    public function getCounterFor(string $credentialId): int
    {
        $credential = $this->findCredential($credentialId);
        if (!$credential instanceof Credential) {
            throw new \InvalidArgumentException('Not found');
        }

        return $credential->getCounter();
    }

    public function updateCounterFor(string $credentialId, int $newCounter): void
    {
        $credential = $this->findCredential($credentialId);
        if (!$credential instanceof Credential) {
            throw new \InvalidArgumentException('Not found');
        }

        $credential->setCounter($newCounter);
        $this->manager->persist($credential);
        $this->manager->flush();
    }
}
