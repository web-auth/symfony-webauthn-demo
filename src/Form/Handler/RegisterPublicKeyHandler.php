<?php

/*
 * This file is part of the appname project.
 *
 * (c) Romain Gautier <mail@romain.sh>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Form\Handler;

use App\Entity\Credential;
use App\Entity\CredentialRepository;
use App\Entity\User;
use App\Entity\UserRepository;
use App\Form\Data\RegisterPublicKey;
use App\Form\Type\RegisterPublicKeyType;
use Psr\Log\LoggerInterface;
use Symfony\Bridge\PsrHttpMessage\HttpMessageFactoryInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Webauthn\AuthenticatorAttestationResponse;
use Webauthn\AuthenticatorAttestationResponseValidator;
use Webauthn\PublicKeyCredentialCreationOptions;
use Webauthn\PublicKeyCredentialDescriptorCollection;
use Webauthn\PublicKeyCredentialLoader;

class RegisterPublicKeyHandler
{
    /**
     * @var FormFactoryInterface
     */
    private $formFactory;

    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @var PublicKeyCredentialLoader
     */
    private $publicKeyCredentialLoader;

    /**
     * @var AuthenticatorAttestationResponseValidator
     */
    private $authenticatorAttestationResponseValidator;

    /**
     * @var CredentialRepository
     */
    private $credentialRepository;

    /**
     * @var HttpMessageFactoryInterface
     */
    private $httpMessageFactory;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(LoggerInterface $logger, HttpMessageFactoryInterface $httpMessageFactory, PublicKeyCredentialLoader $publicKeyCredentialLoader, AuthenticatorAttestationResponseValidator $authenticatorAttestationResponseValidator, FormFactoryInterface $formFactory, UserRepository $userRepository, CredentialRepository $credentialRepository)
    {
        $this->formFactory = $formFactory;
        $this->userRepository = $userRepository;
        $this->publicKeyCredentialLoader = $publicKeyCredentialLoader;
        $this->authenticatorAttestationResponseValidator = $authenticatorAttestationResponseValidator;
        $this->credentialRepository = $credentialRepository;
        $this->httpMessageFactory = $httpMessageFactory;
        $this->logger = $logger;
    }

    public function prepare(RegisterPublicKey $data): FormInterface
    {
        return $this->formFactory->create(RegisterPublicKeyType::class, $data);
    }

    public function process(Request $request, FormInterface $form, PublicKeyCredentialCreationOptions $publicKeyCredentialCreationOptions, User $user): bool
    {
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var RegisterPublicKey $data */
            $data = $form->getData();
            $assertion = $data->getAttestation();
            try {
                $publicKeyCredential = $this->publicKeyCredentialLoader->load($assertion);
                $response = $publicKeyCredential->getResponse();
                if (!$response instanceof AuthenticatorAttestationResponse) {
                    $form->addError(
                        new FormError('Invalid response from the security device')
                    );

                    return false;
                }
                $psr7Request = $this->httpMessageFactory->createRequest($request);
                $this->authenticatorAttestationResponseValidator->check($response, $publicKeyCredentialCreationOptions, $psr7Request);
            } catch (\Throwable $throwable) {
                $this->logger->error(\Safe\sprintf(
                    'Invalid assertion: %s. Request was: %s. Reason is: %s (%s:%d)',
                    $assertion,
                    json_encode($publicKeyCredentialCreationOptions),
                    $throwable->getMessage(),
                    $throwable->getFile(),
                    $throwable->getLine()
                ));
                $form->addError(
                    new FormError('Invalid response from the security device')
                );

                return false;
            }

            $credential = new Credential(
                $response->getAttestationObject()->getAuthData()->getAttestedCredentialData(),
                $response->getAttestationObject()->getAuthData()->getSignCount(),
                $user
            );
            $this->credentialRepository->save($credential);

            $user->addCredential($credential);
            $this->userRepository->save($user);

            return true;
        }

        return false;
    }
}
