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

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;
use Webauthn\Bundle\Security\Authentication\Token\WebauthnToken;

final class AuthenticationSuccessHandler implements AuthenticationSuccessHandlerInterface
{
    public function onAuthenticationSuccess(Request $request, TokenInterface $token): JsonResponse
    {
        $data = [
            'status' => 'ok',
            'errorMessage' => '',
            'username' => $token->getUsername(),
        ];
        if ($token instanceof WebauthnToken) {
            $data += [
                'userEntity' => $token->getPublicKeyCredentialUserEntity(),
                'credentialDescriptor' => $token->getPublicKeyCredentialDescriptor(),
                'isUserPresent' => $token->isUserPresent(),
                'isUserVerified' => $token->isUserVerified(),
            ];
        }

        return new JsonResponse($data, JsonResponse::HTTP_OK);
    }
}