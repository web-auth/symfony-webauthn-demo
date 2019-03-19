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

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Twig\Environment;

final class HomeController
{
    /**
     * @var Environment
     */
    private $twig;

    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    public function __construct(Environment $twig, TokenStorageInterface $tokenStorage)
    {
        $this->twig = $twig;
        $this->tokenStorage = $tokenStorage;
    }

    public function home(): Response
    {
        $page = $this->twig->render('home_react.html.twig', [
            'token' => $this->tokenStorage->getToken(),
        ]);

        return new Response($page);
    }
}
