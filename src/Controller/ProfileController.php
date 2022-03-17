<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class ProfileController extends AbstractController
{
    #[Route('/profile', name: 'profile', methods: [Request::METHOD_GET])]
    public function __invoke(): Response
    {
        return $this->render('profile/index.html.twig', [
            'token' => $this->container->get('security.token_storage')
                ->getToken(),
        ]);
    }
}
