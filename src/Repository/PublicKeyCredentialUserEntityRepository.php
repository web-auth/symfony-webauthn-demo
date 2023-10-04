<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\User;
use LogicException;
use ParagonIE\ConstantTime\Base64UrlSafe;
use Symfony\Component\Uid\Ulid;
use Webauthn\Bundle\Repository\CanGenerateUserEntity;
use Webauthn\Bundle\Repository\CanRegisterUserEntity;
use Webauthn\Bundle\Repository\PublicKeyCredentialUserEntityRepositoryInterface as PublicKeyCredentialUserEntityRepositoryInterface;
use Webauthn\PublicKeyCredentialUserEntity;

final readonly class PublicKeyCredentialUserEntityRepository implements PublicKeyCredentialUserEntityRepositoryInterface, CanRegisterUserEntity, CanGenerateUserEntity
{
    public function __construct(
        private UserRepository $userRepository
    ) {
    }

    public function generateNextUserEntityId(): string
    {
        throw new LogicException('Should never be called');
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

    public function generateUserEntity(?string $username, ?string $displayName): PublicKeyCredentialUserEntity
    {
        $randomUserData = Base64UrlSafe::encodeUnpadded(random_bytes(32));
        return PublicKeyCredentialUserEntity::create(
            $username ?? $randomUserData,
            Ulid::generate(),
            $displayName ?? $randomUserData,
            null
        );
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
