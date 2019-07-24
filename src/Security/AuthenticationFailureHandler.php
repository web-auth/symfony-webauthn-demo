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

namespace App\Security;

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authentication\AuthenticationFailureHandlerInterface;

final class AuthenticationFailureHandler implements AuthenticationFailureHandlerInterface
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): JsonResponse
    {
        $this->logger->debug($request->getContent());
        $this->logger->debug($exception->getFile().'/'.$exception->getLine().': '.$exception->getMessage());
        $this->logger->debug($exception->getTraceAsString());

        $data = [
            'status' => 'error',
            'errorMessage' => $exception->getMessage(),
        ];

        return new JsonResponse($data, Response::HTTP_UNAUTHORIZED);
    }
}
