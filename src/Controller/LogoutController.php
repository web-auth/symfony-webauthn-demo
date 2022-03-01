<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;

final class LogoutController
{
    /**
     * This route is intercepted by the firewall and never reached.
     */
    public function __invoke(): JsonResponse
    {
        return new JsonResponse(['logout']);
    }
}
