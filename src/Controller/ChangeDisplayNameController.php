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

use App\Dto\ChangeDisplayNameRequest;
use App\Entity\User;
use App\Repository\PublicKeyCredentialUserEntityRepository;
use Assert\Assertion;
use RuntimeException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Throwable;
use Webauthn\Bundle\Security\Authentication\Token\WebauthnToken;

final class ChangeDisplayNameController
{
    /**
     * @var PublicKeyCredentialUserEntityRepository
     */
    private $keyCredentialUserEntityRepository;

    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @var ValidatorInterface
     */
    private $validator;

    public function __construct(SerializerInterface $serializer, ValidatorInterface $validator, TokenStorageInterface $tokenStorage, PublicKeyCredentialUserEntityRepository $keyCredentialUserEntityRepository)
    {
        $this->tokenStorage = $tokenStorage;
        $this->keyCredentialUserEntityRepository = $keyCredentialUserEntityRepository;
        $this->serializer = $serializer;
        $this->validator = $validator;
    }

    public function __invoke(Request $request): JsonResponse
    {
        $token = $this->tokenStorage->getToken();
        if (!$token instanceof WebauthnToken) {
            return new JsonResponse([], JsonResponse::HTTP_UNAUTHORIZED);
        }
        $user = $token->getUser();
        if (!$user instanceof User) {
            return new JsonResponse([], JsonResponse::HTTP_UNAUTHORIZED);
        }

        try {
            Assertion::eq('json', $request->getContentType(), 'Only JSON content type allowed');
            $content = $request->getContent();
            Assertion::string($content, 'Invalid data');
            $changeDisplayName = $this->getChangeDisplayNameRequest($content);
            $user->setDisplayName($changeDisplayName->displayName);
            $this->keyCredentialUserEntityRepository->saveUserEntity($user);
        } catch (Throwable $throwable) {
            return new JsonResponse(['status' => 'failed', 'errorMessage' => 'An error occurred'], 400);
        }

        return new JsonResponse([
            'result' => 'ok',
        ]);
    }

    private function getChangeDisplayNameRequest(string $content): ChangeDisplayNameRequest
    {
        $data = $this->serializer->deserialize($content, ChangeDisplayNameRequest::class, 'json');
        Assertion::isInstanceOf($data, ChangeDisplayNameRequest::class, 'Invalid data');
        $errors = $this->validator->validate($data);
        if (\count($errors) > 0) {
            $messages = [];
            foreach ($errors as $error) {
                $messages[] = $error->getPropertyPath().': '.$error->getMessage();
            }
            throw new RuntimeException(implode("\n", $messages));
        }

        return $data;
    }
}
