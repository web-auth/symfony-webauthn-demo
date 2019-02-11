<?php

/*
 * This file is part of the appname project.
 *
 * (c) Romain Gautier <mail@romain.sh>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\EventListener;

use App\Entity\User;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

class FlashMessageOnSuccessfulLogin
{
    public function onSecurityInteractiveLogin(InteractiveLoginEvent $event)
    {
        /** @var User $user */
        $user = $event->getAuthenticationToken()->getUser();
        $event->getRequest()->getSession()->getFlashBag()->add(
            'success',
            \Safe\sprintf('Welcome %s! You have been correctly authenticated.', $user->getDisplayName())
        );
    }
}
