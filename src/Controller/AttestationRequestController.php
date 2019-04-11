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

use Assert\Assertion;
use RuntimeException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Webauthn\AuthenticatorSelectionCriteria;
use Webauthn\Bundle\Repository\PublicKeyCredentialUserEntityRepository;
use Webauthn\Bundle\Service\PublicKeyCredentialCreationOptionsFactory;
use Webauthn\ConformanceToolset\Dto\ServerPublicKeyCredentialCreationOptionsRequest;
use Webauthn\PublicKeyCredentialDescriptor;
use Webauthn\PublicKeyCredentialSource;
use Webauthn\PublicKeyCredentialSourceRepository;
use Webauthn\PublicKeyCredentialUserEntity;

final class AttestationRequestController
{
    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @var PublicKeyCredentialCreationOptionsFactory
     */
    private $publicKeyCredentialCreationOptionsFactory;

    /**
     * @var string
     */
    private $profile;

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
    /**
     * @var string
     */
    private $sessionParameterName;

    public function __construct(SerializerInterface $serializer, ValidatorInterface $validator, PublicKeyCredentialUserEntityRepository $userEntityRepository, PublicKeyCredentialSourceRepository $credentialSourceRepository, PublicKeyCredentialCreationOptionsFactory $publicKeyCredentialCreationOptionsFactory)
    {
        $this->serializer = $serializer;
        $this->validator = $validator;
        $this->publicKeyCredentialCreationOptionsFactory = $publicKeyCredentialCreationOptionsFactory;
        $this->userEntityRepository = $userEntityRepository;
        $this->credentialSourceRepository = $credentialSourceRepository;
        $this->profile = 'default';
        $this->sessionParameterName = 'API_REGISTRATION_OPTIONS';
    }

    public function __invoke(Request $request): JsonResponse
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
                $authenticatorSelection = AuthenticatorSelectionCriteria::createFromArray($authenticatorSelection);
            }
            $publicKeyCredentialCreationOptions = $this->publicKeyCredentialCreationOptionsFactory->create(
                $this->profile,
                $userEntity,
                $excludedCredentials,
                $authenticatorSelection,
                $creationOptionsRequest->attestation
            );
            $data = array_merge(
                ['status' => 'ok', 'errorMessage' => ''],
                $publicKeyCredentialCreationOptions->jsonSerialize()
            );
            $request->getSession()->set($this->sessionParameterName, $publicKeyCredentialCreationOptions);

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
            throw new RuntimeException(implode("\n", $messages));
        }

        return $data;
    }
}
