<?php

declare(strict_types=1);

namespace App\Security;

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;
use Webauthn\Bundle\Security\Authentication\Token\WebauthnToken;

final class AuthenticationSuccessHandler implements AuthenticationSuccessHandlerInterface
{
    public function __construct(
        private LoggerInterface $logger
    ) {
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token): JsonResponse
    {
        $this->logger->error($request->getContent());

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
