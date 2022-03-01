<?php

declare(strict_types=1);

namespace App\EventListener;

use App\Entity\User;
use App\Repository\PublicKeyCredentialUserEntityRepository;
use DateTimeImmutable;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

class LastLoginAt
{
    public function __construct(
        private PublicKeyCredentialUserEntityRepository $userRepository
    ) {
    }

    public function onSecurityInteractiveLogin(InteractiveLoginEvent $event)
    {
        /** @var User $user */
        $user = $event->getAuthenticationToken()
            ->getUser()
        ;
        $user->setLastLoginAt(new DateTimeImmutable());

        $this->userRepository->save($user);
    }
}
