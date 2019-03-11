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
use Ramsey\Uuid\Uuid;
use Webauthn\Bundle\Repository\PublicKeyCredentialUserEntityRepository as BasePublicKeyCredentialUserEntityRepository;
use Webauthn\PublicKeyCredentialUserEntity;

final class PublicKeyCredentialUserEntityRepository implements BasePublicKeyCredentialUserEntityRepository
{
    /**
     * @var UserRepository
     */
    private $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function findOneByUsername(string $username): ?PublicKeyCredentialUserEntity
    {
        return $this->userRepository->find($username);
    }

    public function createUserEntity(string $username, string $displayName, ?string $icon): PublicKeyCredentialUserEntity
    {
        return new PublicKeyCredentialUserEntity($username, Uuid::uuid4()->toString(), $displayName, $icon);
    }

    public function saveUserEntity(PublicKeyCredentialUserEntity $userEntity): void
    {
        $user = new User(
            $userEntity->getId(),
            $userEntity->getName(),
            $userEntity->getDisplayName(),
            []
        );
        $this->userRepository->save($user);
    }
}
