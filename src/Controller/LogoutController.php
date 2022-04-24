<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

final class LogoutController
{
    /**
     * This route is intercepted by the firewall and never reached.
     */
    #[Route('/logout', name: 'logout', methods: [Request::METHOD_GET])]
    public function __invoke(): never
    {
        throw new NotFoundHttpException();
    }
}
