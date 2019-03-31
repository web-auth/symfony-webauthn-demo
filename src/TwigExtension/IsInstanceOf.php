<?php

/*
 * This file is part of the Webauthn Demo project.
 *
 * (c) Florent Morselli
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\TwigExtension;

use Twig\Extension\AbstractExtension;
use Symfony\Component\Security\Core\Authentication\Token\RememberMeToken;
use Twig\TwigTest;
use Webauthn\SecurityBundle\Security\Authentication\Token\WebauthnToken;
use Webauthn\TrustPath\CertificateTrustPath;
use Webauthn\TrustPath\EcdaaKeyIdTrustPath;
use Webauthn\TrustPath\EmptyTrustPath;

class IsInstanceOf extends AbstractExtension
{
    public function getTests()
    {
        return [
            new TwigTest('isWebauthnToken', [$this, 'isWebauthnToken']),
            new TwigTest('isRememberMeToken', [$this, 'isRememberMeToken']),
            new TwigTest('isCertificateTrustPath', [$this, 'isCertificateTrustPath']),
            new TwigTest('isEcdaaKeyIdTrustPath', [$this, 'isEcdaaKeyIdTrustPath']),
            new TwigTest('isEmptyTrustPath', [$this, 'isEmptyTrustPath']),
        ];
    }

    /**
     * @param mixed $var
     */
    public function isWebauthnToken($var): bool
    {
        return $var instanceof WebauthnToken;
    }

    /**
     * @param mixed $var
     */
    public function isRememberMeToken($var): bool
    {
        return $var instanceof RememberMeToken;
    }

    /**
     * @param mixed $var
     */
    public function isCertificateTrustPath($var): bool
    {
        return $var instanceof CertificateTrustPath;
    }

    /**
     * @param mixed $var
     */
    public function isEcdaaKeyIdTrustPath($var): bool
    {
        return $var instanceof EcdaaKeyIdTrustPath;
    }

    /**
     * @param mixed $var
     */
    public function isEmptyTrustPath($var): bool
    {
        return $var instanceof EmptyTrustPath;
    }
}
