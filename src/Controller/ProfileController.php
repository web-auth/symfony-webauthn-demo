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

use App\Entity\User;
use App\Entity\UserEntity;
use App\Repository\PublicKeyCredentialSourceRepository;
use App\Repository\PublicKeyCredentialUserEntityRepository;
use function Safe\json_encode;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Webauthn\JsonSecurityBundle\Security\Authentication\Token\WebauthnToken;

final class ProfileController
{
    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;
    /**
     * @var PublicKeyCredentialSourceRepository
     */
    private $keyCredentialSourceRepository;
    /**
     * @var PublicKeyCredentialUserEntityRepository
     */
    private $keyCredentialUserEntityRepository;

    public function __construct(TokenStorageInterface $tokenStorage, PublicKeyCredentialUserEntityRepository $keyCredentialUserEntityRepository, PublicKeyCredentialSourceRepository $keyCredentialSourceRepository)
    {
        $this->tokenStorage = $tokenStorage;
        $this->keyCredentialSourceRepository = $keyCredentialSourceRepository;
        $this->keyCredentialUserEntityRepository = $keyCredentialUserEntityRepository;
    }


    public function __invoke(): JsonResponse
    {
        $token = $this->tokenStorage->getToken();
        if (!$token instanceof WebauthnToken) {
            return new JsonResponse([], JsonResponse::HTTP_UNAUTHORIZED);
        }
        $user = $token->getUser();
        if (!$user instanceof User) {
            return new JsonResponse([], JsonResponse::HTTP_UNAUTHORIZED);
        }
        $userEntity = $this->keyCredentialUserEntityRepository->findOneByUsername($user->getUsername());
        if (!$userEntity instanceof UserEntity) {
            return new JsonResponse([], JsonResponse::HTTP_UNAUTHORIZED);
        }
        $credentials = $this->keyCredentialSourceRepository->findAllForUserEntity($userEntity);

        return new JsonResponse([
            'isUserPresent' => $token->isUserPresent(),
            'isUserVerified' => $token->isUserVerified(),
            'userForAuthentication' => $token->getCredentials(),
            'user' => $userEntity,
            'created_at' => $user->getCreatedAt()->format('c'),
            'last_login_at' => $user->getLastLoginAt() ? $user->getLastLoginAt()->format('c') : 'never logged in',
            'credentials' => $credentials,
        ]);
    }
}
