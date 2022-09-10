<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\User;
use Symfony\Component\Uid\Ulid;
use Webauthn\Bundle\Repository\PublicKeyCredentialUserEntityRepository as PublicKeyCredentialUserEntityRepositoryInterface;
use Webauthn\PublicKeyCredentialUserEntity;

final class PublicKeyCredentialUserEntityRepository implements PublicKeyCredentialUserEntityRepositoryInterface
{
    public function __construct(private readonly UserRepository $userRepository)
    {
    }

    public function generateNextUserEntityId(): string
    {
        return Ulid::generate();
    }

    public function saveUserEntity(PublicKeyCredentialUserEntity $userEntity): void
    {
        /** @var User|null $user */
        $user = $this->userRepository->findOneBy([
            'id' => $userEntity->getId(),
        ]);
        if ($user === null) {
            $user = new User($userEntity->getId(), $userEntity->getName(), $userEntity->getDisplayName());
        } else {
            if ($user->getDisplayName() !== $userEntity->getDisplayName()) {
                $user->setDisplayName($userEntity->getDisplayName());
            }
            if ($user->getUserIdentifier() !== $userEntity->getName()) {
                $user->setUsername($userEntity->getName());
            }
        }

        $this->userRepository->save($user);
    }

    public function findOneByUsername(string $username): ?PublicKeyCredentialUserEntity
    {
        /** @var User|null $user */
        $user = $this->userRepository->findOneBy([
            'username' => $username,
        ]);

        return $this->getUserEntity($user);
    }

    public function findOneByUserHandle(string $userHandle): ?PublicKeyCredentialUserEntity
    {
        /** @var User|null $user */
        $user = $this->userRepository->findOneBy([
            'id' => $userHandle,
        ]);

        return $this->getUserEntity($user);
    }

    private function getUserEntity(null|User $user): ?PublicKeyCredentialUserEntity
    {
        if ($user === null) {
            return null;
        }

        return new PublicKeyCredentialUserEntity(
            $user->getUserIdentifier(),
            $user->getId(),
            $user->getDisplayName(),
            null
        );
    }
}
