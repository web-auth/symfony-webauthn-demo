<?php

declare(strict_types=1);

namespace App\Security;

use App\Entity\User;
use App\Repository\PublicKeyCredentialUserEntityRepository;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

final class UserProvider implements UserProviderInterface
{
    public function __construct(
        private PublicKeyCredentialUserEntityRepository $userRepository
    ) {
    }

    public function loadUserByUsername($username)
    {
        $user = $this->userRepository->find($username);
        if (! $user) {
            throw new UsernameNotFoundException(sprintf('User "%s" not found.', $username));
        }

        return $user;
    }

    public function refreshUser(UserInterface $user)
    {
        return $this->userRepository->find($user->getUsername());
    }

    public function supportsClass($class)
    {
        return $class === User::class;
    }
}
