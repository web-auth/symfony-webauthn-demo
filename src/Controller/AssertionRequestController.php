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

use App\Dto\ServerPublicKeyCredentialRequestOptionsRequest;
use App\Repository\PublicKeyCredentialUserEntityRepository;
use Assert\Assertion;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Webauthn\Bundle\Service\PublicKeyCredentialRequestOptionsFactory;
use Webauthn\PublicKeyCredentialDescriptor;
use Webauthn\PublicKeyCredentialSource;
use Webauthn\PublicKeyCredentialSourceRepository;
use Webauthn\PublicKeyCredentialUserEntity;

final class AssertionRequestController
{
    private const SESSION_PARAMETER = '__WEBAUTHN__ASSERTION__REQUEST__';

    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @var PublicKeyCredentialRequestOptionsFactory
     */
    private $publicKeyCredentialRequestOptionsFactory;

    /**
     * @var ValidatorInterface
     */
    private $validator;

    /**
     * @var PublicKeyCredentialUserEntityRepository
     */
    private $userEntityRepository;

    /**
     * @var PublicKeyCredentialSourceRepository
     */
    private $credentialSourceRepository;

    public function __construct(SerializerInterface $serializer, ValidatorInterface $validator, PublicKeyCredentialUserEntityRepository $userEntityRepository, PublicKeyCredentialSourceRepository $credentialSourceRepository, PublicKeyCredentialRequestOptionsFactory $publicKeyCredentialRequestOptionsFactory)
    {
        $this->serializer = $serializer;
        $this->validator = $validator;
        $this->publicKeyCredentialRequestOptionsFactory = $publicKeyCredentialRequestOptionsFactory;
        $this->userEntityRepository = $userEntityRepository;
        $this->credentialSourceRepository = $credentialSourceRepository;
    }

    public function __invoke(Request $request): Response
    {
        try {
            Assertion::eq('json', $request->getContentType(), 'Only JSON content type allowed');
            $content = $request->getContent();
            Assertion::string($content, 'Invalid data');
            $creationOptionsRequest = $this->getServerPublicKeyCredentialRequestOptionsRequest($content);
            $userEntity = $this->getUserEntity($creationOptionsRequest);
            $allowedCredentials = $this->getCredentials($userEntity);
            $publicKeyCredentialRequestOptions = $this->publicKeyCredentialRequestOptionsFactory->create(
                'default',
                $allowedCredentials,
                $creationOptionsRequest->userVerification
            );
            $data = array_merge(
                ['status' => 'ok', 'errorMessage' => ''],
                $publicKeyCredentialRequestOptions->jsonSerialize()
            );
            $request->getSession()->set(self::SESSION_PARAMETER, ['options' => $publicKeyCredentialRequestOptions, 'userEntity' => $userEntity]);

            return new JsonResponse($data);
        } catch (\Throwable $throwable) {
            return new JsonResponse(['status' => 'failed', 'errorMessage' => $throwable->getMessage()], 400);
        }
    }

    /**
     * @return PublicKeyCredentialDescriptor[]
     */
    private function getCredentials(PublicKeyCredentialUserEntity $userEntity): array
    {
        $credentialSources = $this->credentialSourceRepository->findAllForUserEntity($userEntity);

        return array_map(function (PublicKeyCredentialSource $credential) {
            return $credential->getPublicKeyCredentialDescriptor();
        }, $credentialSources);
    }

    private function getUserEntity(ServerPublicKeyCredentialRequestOptionsRequest $creationOptionsRequest): PublicKeyCredentialUserEntity
    {
        $username = $creationOptionsRequest->username;
        $userEntity = $this->userEntityRepository->findOneByUsername($username);
        Assertion::notNull($userEntity, 'User not found');

        return $userEntity;
    }

    private function getServerPublicKeyCredentialRequestOptionsRequest(string $content): ServerPublicKeyCredentialRequestOptionsRequest
    {
        $data = $this->serializer->deserialize($content, ServerPublicKeyCredentialRequestOptionsRequest::class, 'json');
        Assertion::isInstanceOf($data, ServerPublicKeyCredentialRequestOptionsRequest::class, 'Invalid data');
        $errors = $this->validator->validate($data);
        if (\count($errors) > 0) {
            $messages = [];
            foreach ($errors as $error) {
                $messages[] = $error->getPropertyPath().': '.$error->getMessage();
            }
            throw new \RuntimeException(implode("\n", $messages));
        }

        return $data;
    }
}
