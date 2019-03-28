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

use App\Form\Handler\RegisterPublicKeyHandler;
use App\Form\Handler\RegisterUserHandler;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Twig\Environment;
use Webauthn\Bundle\Service\PublicKeyCredentialCreationOptionsFactory;
use Webauthn\SecurityBundle\Security\WebauthnUtils;

final class SecurityController
{
    public const USER_REGISTRATION_DATA = 'USER_REGISTRATION_DATA';
    public const USER_REGISTRATION_REQUEST = 'USER_REGISTRATION_REQUEST';

    /**
     * @var Environment
     */
    private $twig;

    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * @var WebauthnUtils
     */
    private $webauthnUtils;

    /**
     * @var RegisterUserHandler
     */
    private $registerUserHandler;

    /**
     * @var RegisterPublicKeyHandler
     */
    private $registerPublicKeyHandler;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var PublicKeyCredentialCreationOptionsFactory
     */
    private $publicKeyCredentialCreationOptionsFactory;

    public function __construct(PublicKeyCredentialCreationOptionsFactory $publicKeyCredentialCreationOptionsFactory, RouterInterface $router, RegisterUserHandler $registerUserHandler, RegisterPublicKeyHandler $registerPublicKeyHandler, Environment $twig, TokenStorageInterface $tokenStorage, WebauthnUtils $webauthnUtils)
    {
        $this->twig = $twig;
        $this->tokenStorage = $tokenStorage;
        $this->webauthnUtils = $webauthnUtils;
        $this->registerUserHandler = $registerUserHandler;
        $this->registerPublicKeyHandler = $registerPublicKeyHandler;
        $this->router = $router;
        $this->publicKeyCredentialCreationOptionsFactory = $publicKeyCredentialCreationOptionsFactory;
    }

    public function login(): Response
    {
        $error = $this->webauthnUtils->getLastAuthenticationError();
        $lastUsername = $this->webauthnUtils->getLastUsername();

        $page = $this->twig->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);

        return new Response($page);
    }

    public function assertion(Request $request): Response
    {
        /** @var UserInterface $user */
        $user = $this->tokenStorage->getToken()->getUser();
        $rememberMe = $this->tokenStorage->getToken()->isRememberMe();
        $publicKeyCredentialRequestOptions = $this->webauthnUtils->generateRequestFromProfile('default', $user);
        $request->getSession()->set('foo.bar', $publicKeyCredentialRequestOptions);
        $error = $this->webauthnUtils->getLastAuthenticationError();

        $page = $this->twig->render('security/assertion.html.twig', [
            'error' => $error,
            'user' => $user,
            'publicKeyCredentialRequestOptions' => $publicKeyCredentialRequestOptions,
            'rememberMe' => $rememberMe,
        ]);

        return new Response($page);
    }

    /** This route is intercepted by the firewall and never reached */
    public function abort(): Response
    {
        return new Response('Abort');
    }

    /** This route is intercepted by the firewall and never reached */
    public function logout(): Response
    {
        return new Response('Logout');
    }
}
