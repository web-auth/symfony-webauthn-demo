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

final class AssertionRequestController
{
    /** This route is intercepted by the firewall and never reached */
    public function __invoke(): JsonResponse
    {
        return new JsonResponse(['options']);
    }
}
