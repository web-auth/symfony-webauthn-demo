<?php

declare(strict_types=1);

namespace App\Security;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface;

final readonly class MainEntryPoint implements AuthenticationEntryPointInterface
{
    public function __construct(
        private RouterInterface $router
    ) {
    }

    public function start(Request $request, AuthenticationException $authException = null): Response
    {
        $url = $this->router->generate('login');

        return new RedirectResponse($url, Response::HTTP_TEMPORARY_REDIRECT);
    }
}
