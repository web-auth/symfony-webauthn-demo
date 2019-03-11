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

use App\Entity\PublicKeyCredentialSource;
use App\Entity\User;
use Doctrine\Common\Persistence\ManagerRegistry;
use Webauthn\Bundle\Repository\PublicKeyCredentialSourceRepository as BasePublicKeyCredentialSourceRepository;

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
        $qb = $this->getEntityManager()->createQueryBuilder();

        return $qb->select('c')
            ->from($this->getClass(), 'c')
            ->where('c.userHandle = :user_handle')
            ->setParameter(':user_handle', $user->getUserHandle())
            ->getQuery()
            ->execute()
        ;
    }
}
