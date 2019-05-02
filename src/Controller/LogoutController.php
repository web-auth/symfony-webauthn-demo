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

namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;

final class LogoutController
{
    /** This route is intercepted by the firewall and never reached */
    public function __invoke(): JsonResponse
    {
        return new JsonResponse(['logout']);
    }
}
