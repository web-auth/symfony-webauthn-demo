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

use App\Dto\ServerPublicKeyCredentialCreationOptionsRequest;
use App\Repository\PublicKeyCredentialUserEntityRepository;
use Assert\Assertion;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Webauthn\AuthenticatorSelectionCriteria;
use Webauthn\Bundle\Service\PublicKeyCredentialCreationOptionsFactory;
use Webauthn\PublicKeyCredentialDescriptor;
use Webauthn\PublicKeyCredentialSource;
use Webauthn\PublicKeyCredentialSourceRepository;
use Webauthn\PublicKeyCredentialUserEntity;

final class AttestationRequestController
{
    private const SESSION_PARAMETER = '__WEBAUTHN__ATTESTATION__REQUEST__';

    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @var PublicKeyCredentialCreationOptionsFactory
     */
    private $publicKeyCredentialCreationOptionsFactory;

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

    public function __construct(SerializerInterface $serializer, ValidatorInterface $validator, PublicKeyCredentialUserEntityRepository $userEntityRepository, PublicKeyCredentialSourceRepository $credentialSourceRepository, PublicKeyCredentialCreationOptionsFactory $publicKeyCredentialCreationOptionsFactory)
    {
        $this->serializer = $serializer;
        $this->validator = $validator;
        $this->publicKeyCredentialCreationOptionsFactory = $publicKeyCredentialCreationOptionsFactory;
        $this->userEntityRepository = $userEntityRepository;
        $this->credentialSourceRepository = $credentialSourceRepository;
    }

    public function __invoke(Request $request): Response
    {
        try {
            Assertion::eq('json', $request->getContentType(), 'Only JSON content type allowed');
            $content = $request->getContent();
            Assertion::string($content, 'Invalid data');
            $creationOptionsRequest = $this->getServerPublicKeyCredentialCreationOptionsRequest($content);
            $userEntity = $this->getUserEntity($creationOptionsRequest);
            $excludedCredentials = $this->getCredentials($userEntity);
            $authenticatorSelection = $creationOptionsRequest->authenticatorSelection;
            if (\is_array($authenticatorSelection)) {
                $authenticatorSelection = AuthenticatorSelectionCriteria::createFromJson($authenticatorSelection);
            }
            $publicKeyCredentialCreationOptions = $this->publicKeyCredentialCreationOptionsFactory->create(
                'default',
                $userEntity,
                $excludedCredentials,
                $authenticatorSelection,
                $creationOptionsRequest->attestation
            );
            $data = array_merge(
                ['status' => 'ok', 'errorMessage' => ''],
                $publicKeyCredentialCreationOptions->jsonSerialize()
            );
            $request->getSession()->set(self::SESSION_PARAMETER, $publicKeyCredentialCreationOptions);

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

    private function getUserEntity(ServerPublicKeyCredentialCreationOptionsRequest $creationOptionsRequest): PublicKeyCredentialUserEntity
    {
        $username = $creationOptionsRequest->username;
        $userEntity = $this->userEntityRepository->findOneByUsername($username);
        if (null === $userEntity) {
            $userEntity = $this->userEntityRepository->createUserEntity($username, $creationOptionsRequest->displayName, null);
        }

        return $userEntity;
    }

    private function getServerPublicKeyCredentialCreationOptionsRequest(string $content): ServerPublicKeyCredentialCreationOptionsRequest
    {
        $data = $this->serializer->deserialize($content, ServerPublicKeyCredentialCreationOptionsRequest::class, 'json');
        Assertion::isInstanceOf($data, ServerPublicKeyCredentialCreationOptionsRequest::class, 'Invalid data');
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
