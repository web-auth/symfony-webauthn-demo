<?php

declare(strict_types=1);

/*
 * This file is part of the appname project.
 *
 * (c) Romain Gautier <mail@romain.sh>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Controller;

use App\Entity\User;
use App\Form\Data\RegisterPublicKey;
use App\Form\Data\RegisterUser;
use App\Form\Handler\RegisterPublicKeyHandler;
use App\Form\Handler\RegisterUserHandler;
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Twig\Environment;
use Webauthn\Bundle\Service\PublicKeyCredentialCreationOptionsFactory;
use Webauthn\Bundle\Service\PublicKeyCredentialRequestOptionsFactory;
use Webauthn\PublicKeyCredentialCreationOptions;
use Webauthn\PublicKeyCredentialUserEntity;
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
    /**
     * @var PublicKeyCredentialRequestOptionsFactory
     */
    private $publicKeyCredentialRequestOptionsFactory;

    public function __construct(PublicKeyCredentialRequestOptionsFactory $publicKeyCredentialRequestOptionsFactory, PublicKeyCredentialCreationOptionsFactory $publicKeyCredentialCreationOptionsFactory, RouterInterface $router, RegisterUserHandler $registerUserHandler, RegisterPublicKeyHandler $registerPublicKeyHandler, Environment $twig, TokenStorageInterface $tokenStorage, WebauthnUtils $webauthnUtils)
    {
        $this->twig = $twig;
        $this->tokenStorage = $tokenStorage;
        $this->webauthnUtils = $webauthnUtils;
        $this->registerUserHandler = $registerUserHandler;
        $this->registerPublicKeyHandler = $registerPublicKeyHandler;
        $this->router = $router;
        $this->publicKeyCredentialCreationOptionsFactory = $publicKeyCredentialCreationOptionsFactory;
        $this->publicKeyCredentialRequestOptionsFactory = $publicKeyCredentialRequestOptionsFactory;
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

    public function registerUser(Request $request): Response
    {
        $request->getSession()->remove(self::USER_REGISTRATION_DATA);
        $request->getSession()->remove(self::USER_REGISTRATION_REQUEST);
        $data = new RegisterUser();
        $form = $this->registerUserHandler->prepare($data);
        if ($this->registerUserHandler->process($request, $form)) {
            $request->getSession()->set(self::USER_REGISTRATION_DATA, $form->getData());

            return new RedirectResponse(
                $this->router->generate('app_register_public_key')
            );
        }

        $page = $this->twig->render('security/register_user.html.twig', [
            'form' => $form->createView(),
        ]);

        return new Response($page);
    }

    public function registerPublicKey(Request $request): Response
    {
        $registerUser = $request->getSession()->get(self::USER_REGISTRATION_DATA);
        if (!$registerUser instanceof RegisterUser) {
            return new RedirectResponse(
                $this->router->generate('app_register_user')
            );
        }

        $data = new RegisterPublicKey();
        $form = $this->registerPublicKeyHandler->prepare($data);
        $publicKeyCredentialCreationOptions = $this->getPublicKeyCredentialCreationOptions($request, $registerUser);
        $user = $this->getUser($publicKeyCredentialCreationOptions);
        if ($this->registerPublicKeyHandler->process($request, $form, $publicKeyCredentialCreationOptions, $user)) {
            $request->getSession()->getFlashBag()->add(
                'success',
                sprintf('The account with username "%s" has correctly been created and security device is now associated to that account', $registerUser->getUsername())
            );

            return new RedirectResponse(
                //Add flash message
                $this->router->generate('app_home')
            );
        }

        $page = $this->twig->render('security/register_user_attestation.html.twig', [
            'form' => $form->createView(),
            'publicKeyCredentialCreationOptions' => $publicKeyCredentialCreationOptions,
        ]);

        return new Response($page);
    }

    private function getUser(PublicKeyCredentialCreationOptions $publicKeyCredentialCreationOptions): User
    {
        return new User(
            $publicKeyCredentialCreationOptions->getUser()->getId(),
            $publicKeyCredentialCreationOptions->getUser()->getName(),
            $publicKeyCredentialCreationOptions->getUser()->getDisplayName(),
            []
        );
    }

    private function getPublicKeyCredentialCreationOptions(Request $request, RegisterUser $registerUser): PublicKeyCredentialCreationOptions
    {
        $publicKeyCredentialCreationOptions = $request->getSession()->get(SecurityController::USER_REGISTRATION_REQUEST);
        if (!$publicKeyCredentialCreationOptions instanceof PublicKeyCredentialCreationOptions) {
            $publicKeyCredentialCreationOptions = $this->publicKeyCredentialCreationOptionsFactory->create(
                'default',
                new PublicKeyCredentialUserEntity($registerUser->getUsername(), Uuid::uuid4()->toString(), $registerUser->getDisplayName(), null)
            );
            $request->getSession()->set(self::USER_REGISTRATION_REQUEST, $publicKeyCredentialCreationOptions);
        }

        return $publicKeyCredentialCreationOptions;
    }
}
