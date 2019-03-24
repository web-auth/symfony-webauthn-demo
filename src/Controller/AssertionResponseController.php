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

use App\Repository\PublicKeyCredentialUserEntityRepository;
use Assert\Assertion;
use Symfony\Bridge\PsrHttpMessage\HttpMessageFactoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Webauthn\AuthenticatorAssertionResponse;
use Webauthn\AuthenticatorAssertionResponseValidator;
use Webauthn\PublicKeyCredentialLoader;
use Webauthn\PublicKeyCredentialRequestOptions;
use Webauthn\PublicKeyCredentialSourceRepository;
use Webauthn\PublicKeyCredentialUserEntity;

final class AssertionResponseController
{
    private const SESSION_PARAMETER = '__WEBAUTHN__ASSERTION__REQUEST__';

    /**
     * @var PublicKeyCredentialUserEntityRepository
     */
    private $userEntityRepository;

    /**
     * @var PublicKeyCredentialSourceRepository
     */
    private $credentialSourceRepository;

    /**
     * @var PublicKeyCredentialLoader
     */
    private $publicKeyCredentialLoader;

    /**
     * @var AuthenticatorAssertionResponseValidator
     */
    private $assertionResponseValidator;

    /**
     * @var HttpMessageFactoryInterface
     */
    private $httpMessageFactory;

    public function __construct(HttpMessageFactoryInterface $httpMessageFactory, PublicKeyCredentialLoader $publicKeyCredentialLoader, AuthenticatorAssertionResponseValidator $assertionResponseValidator, PublicKeyCredentialUserEntityRepository $userEntityRepository, PublicKeyCredentialSourceRepository $credentialSourceRepository)
    {
        $this->httpMessageFactory = $httpMessageFactory;
        $this->assertionResponseValidator = $assertionResponseValidator;
        $this->userEntityRepository = $userEntityRepository;
        $this->credentialSourceRepository = $credentialSourceRepository;
        $this->publicKeyCredentialLoader = $publicKeyCredentialLoader;
    }

    public function __invoke(Request $request): Response
    {
        try {
            $psr7Request = $this->httpMessageFactory->createRequest($request);
            Assertion::eq('json', $request->getContentType(), 'Only JSON content type allowed');
            $content = $request->getContent();
            Assertion::string($content, 'Invalid data');
            $publicKeyCredential = $this->publicKeyCredentialLoader->load($content);
            $response = $publicKeyCredential->getResponse();
            Assertion::isInstanceOf($response, AuthenticatorAssertionResponse::class, 'Invalid response');
            $data = $request->getSession()->get(self::SESSION_PARAMETER);
            $request->getSession()->remove(self::SESSION_PARAMETER);
            Assertion::isArray($data, 'Unable to find the public key credential creation options');
            Assertion::keyExists($data, 'options', 'Unable to find the public key credential creation options');
            $publicKeyCredentialRequestOptions = $data['options'];
            Assertion::isInstanceOf($publicKeyCredentialRequestOptions, PublicKeyCredentialRequestOptions::class, 'Unable to find the public key credential creation options');
            Assertion::keyExists($data, 'userEntity', 'Unable to find the public key credential creation options');
            $userEntity = $data['userEntity'];
            Assertion::isInstanceOf(PublicKeyCredentialUserEntity::class, $userEntity, 'Unable to find the public key credential creation options');
            Assertion::notEmpty($userEntity, 'Unable to find the public key credential creation options');
            $this->assertionResponseValidator->check($publicKeyCredential->getId(), $response, $publicKeyCredentialRequestOptions, $psr7Request, $userEntity->getId());

            return new JsonResponse(['status' => 'ok', 'errorMessage' => '']);
        } catch (\Throwable $throwable) {
            return new JsonResponse(['status' => 'failed', 'errorMessage' => $throwable->getMessage()], 400);
        }
    }
}
