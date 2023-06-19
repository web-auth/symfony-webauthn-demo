<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use App\Entity\User;
use App\Repository\UserRepository;
use Psr\Clock\ClockInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Http\Event\LoginSuccessEvent;

final readonly class SecuritySubscriber implements EventSubscriberInterface
{
    public function __construct(
        private UserRepository $userRepository,
        private ClockInterface $clock,
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            LoginSuccessEvent::class => 'onLogin',
        ];
    }

    public function onLogin(LoginSuccessEvent $event): void
    {
        $user = $event->getUser();
        if (! $user instanceof User) {
            return;
        }
        $user->setLastLoginAt($this->clock->now());
        $this->userRepository->save($user);
    }
}
