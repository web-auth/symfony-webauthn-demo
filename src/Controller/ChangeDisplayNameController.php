<?php

declare(strict_types=1);

namespace App\Controller;

use App\Dto\ChangeDisplayNameRequest;
use App\Entity\User;
use App\Repository\PublicKeyCredentialUserEntityRepository;
use Assert\Assertion;
use function count;
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
    public function __construct(
        private SerializerInterface $serializer,
        private ValidatorInterface $validator,
        private TokenStorageInterface $tokenStorage,
        private PublicKeyCredentialUserEntityRepository $keyCredentialUserEntityRepository
    ) {
    }

    public function __invoke(Request $request): JsonResponse
    {
        $token = $this->tokenStorage->getToken();
        if (! $token instanceof WebauthnToken) {
            return new JsonResponse([], JsonResponse::HTTP_UNAUTHORIZED);
        }
        $user = $token->getUser();
        if (! $user instanceof User) {
            return new JsonResponse([], JsonResponse::HTTP_UNAUTHORIZED);
        }

        try {
            Assertion::eq('json', $request->getContentType(), 'Only JSON content type allowed');
            $content = $request->getContent();
            Assertion::string($content, 'Invalid data');
            $changeDisplayName = $this->getChangeDisplayNameRequest($content);
            $user->setDisplayName($changeDisplayName->displayName);
            $this->keyCredentialUserEntityRepository->saveUserEntity($user);
        } catch (Throwable) {
            return new JsonResponse([
                'status' => 'failed',
                'errorMessage' => 'An error occurred',
            ], 400);
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
        if (count($errors) > 0) {
            $messages = [];
            foreach ($errors as $error) {
                $messages[] = $error->getPropertyPath() . ': ' . $error->getMessage();
            }
            throw new RuntimeException(implode("\n", $messages));
        }

        return $data;
    }
}
