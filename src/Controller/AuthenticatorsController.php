<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Webauthn\Bundle\Repository\PublicKeyCredentialUserEntityRepository;
use Webauthn\PublicKeyCredentialSource;
use Webauthn\PublicKeyCredentialSourceRepository;

final class AuthenticatorsController extends AbstractController
{
    public function __construct(
        private readonly PublicKeyCredentialUserEntityRepository $keyCredentialUserEntityRepository,
        private readonly PublicKeyCredentialSourceRepository $keyCredentialSourceRepository
    ) {
    }

    #[Route('/profile/authenticators', name: 'authenticators', methods: [Request::METHOD_GET])]
    public function __invoke(): Response
    {
        $user = $this->getUser();
        if (! $user instanceof User) {
            throw new AccessDeniedHttpException();
        }

        $userEntity = $this->keyCredentialUserEntityRepository->findOneByUserHandle($user->getId());
        if ($userEntity === null) {
            throw new AccessDeniedHttpException();
        }

        $credentials = $this->keyCredentialSourceRepository->findAllForUserEntity($userEntity);
        $credentials = array_map(static function (PublicKeyCredentialSource $source) {
            $data = $source->jsonSerialize();
            $data['aaguid'] = $source->getAaguid()->toRfc4122();

            return $data;
        }, $credentials);

        return $this->render('authenticators/index.html.twig', [
            'credentials' => $credentials,
        ]);
    }
}
