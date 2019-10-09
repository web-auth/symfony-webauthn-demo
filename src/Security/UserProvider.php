<?php

declare(strict_types=1);

/*
 * The MIT License (MIT)
 *
 * Copyright (c) 2014-2019 Spomky-Labs
 *
 * This software may be modified and distributed under the terms
 * of the MIT license.  See the LICENSE file for details.
 */

namespace App\Security;

use App\Entity\User;
use App\Repository\PublicKeyCredentialUserEntityRepository;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

final class UserProvider implements UserProviderInterface
{
    /**
     * @var PublicKeyCredentialUserEntityRepository
     */
    private $userRepository;

    public function __construct(PublicKeyCredentialUserEntityRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function loadUserByUsername($username)
    {
        $user = $this->userRepository->find($username);
        if (!$user) {
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
        return User::class === $class;
    }
}
