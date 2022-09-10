<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\PublicKeyCredentialSource;
use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use Webauthn\Bundle\Repository\PublicKeyCredentialSourceRepository as BasePublicKeyCredentialSourceRepository;
use Webauthn\PublicKeyCredentialSource as BasePublicKeyCredentialSource;

final class PublicKeyCredentialSourceRepository extends BasePublicKeyCredentialSourceRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PublicKeyCredentialSource::class);
    }

    /**
     * @return PublicKeyCredentialSource[]
     */
    public function allForUser(User $user): array
    {
        $qb = $this->getEntityManager()
            ->createQueryBuilder()
        ;

        return $qb->select('c')
            ->from($this->getClass(), 'c')
            ->where('c.userHandle = :user_handle')
            ->setParameter(':user_handle', $user->getId())
            ->getQuery()
            ->execute()
        ;
    }

    public function saveCredentialSource(
        BasePublicKeyCredentialSource $publicKeyCredentialSource,
        bool $flush = true
    ): void {
        if (! $publicKeyCredentialSource instanceof PublicKeyCredentialSource) {
            $publicKeyCredentialSource = new PublicKeyCredentialSource(
                $publicKeyCredentialSource->getPublicKeyCredentialId(),
                $publicKeyCredentialSource->getType(),
                $publicKeyCredentialSource->getTransports(),
                $publicKeyCredentialSource->getAttestationType(),
                $publicKeyCredentialSource->getTrustPath(),
                $publicKeyCredentialSource->getAaguid(),
                $publicKeyCredentialSource->getCredentialPublicKey(),
                $publicKeyCredentialSource->getUserHandle(),
                $publicKeyCredentialSource->getCounter()
            );
        }
        parent::saveCredentialSource($publicKeyCredentialSource, $flush);
    }
}
