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

namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;

final class SecurityController
{
    /** This route is intercepted by the firewall and never reached */
    public function options(): JsonResponse
    {
        return new JsonResponse(['options']);
    }

    /** This route is intercepted by the firewall and never reached */
    public function login(): JsonResponse
    {
        return new JsonResponse(['login']);
    }

    /** This route is intercepted by the firewall and never reached */
    public function logout(): JsonResponse
    {
        return new JsonResponse(['logout']);
    }
}
