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

namespace App\EventListener;

use App\Entity\User;
use App\Repository\PublicKeyCredentialUserEntityRepository;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

class LastLoginAt
{
    /**
     * @var PublicKeyCredentialUserEntityRepository
     */
    private $userRepository;

    public function __construct(PublicKeyCredentialUserEntityRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function onSecurityInteractiveLogin(InteractiveLoginEvent $event)
    {
        /** @var User $user */
        $user = $event->getAuthenticationToken()->getUser();
        $user->setLastLoginAt(new \DateTimeImmutable());

        $this->userRepository->save($user);
    }
}
