<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use App\Repository\PublicKeyCredentialSourceRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Webauthn\Bundle\Security\Authentication\Token\WebauthnToken;
use Webauthn\PublicKeyCredentialSource;

final class ProfileController
{
    public function __construct(
        private TokenStorageInterface $tokenStorage,
        private PublicKeyCredentialSourceRepository $keyCredentialSourceRepository
    ) {
    }

    public function __invoke(): JsonResponse
    {
        $token = $this->tokenStorage->getToken();
        if (! $token instanceof WebauthnToken) {
            return new JsonResponse([], JsonResponse::HTTP_UNAUTHORIZED);
        }
        $user = $token->getUser();
        if (! $user instanceof User) {
            return new JsonResponse([], JsonResponse::HTTP_UNAUTHORIZED);
        }
        $credentials = $this->keyCredentialSourceRepository->findAllForUserEntity($user);
        $credentials = array_map(static function (PublicKeyCredentialSource $source) {
            $data = $source->jsonSerialize();
            $data['aaguid'] = $source->getAaguid()->toRfc4122();

            return $data;
        }, $credentials);

        return new JsonResponse([
            'isUserPresent' => $token->isUserPresent(),
            'isUserVerified' => $token->isUserVerified(),
            'userForAuthentication' => $token->getCredentials(),
            'user' => $user,
            'created_at' => $user->getCreatedAt()
                ->format('c'),
            'last_login_at' => $user->getLastLoginAt()
                ?->format('c'),
            'credentials' => $credentials,
        ]);
    }
}
