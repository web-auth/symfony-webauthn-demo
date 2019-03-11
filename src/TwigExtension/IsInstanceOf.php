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

use Symfony\Component\Security\Core\Authentication\Token\RememberMeToken;
use Webauthn\SecurityBundle\Security\Authentication\Token\WebauthnToken;

class IsInstanceOf extends \Twig_Extension
{
    public function getTests()
    {
        return [
            new \Twig_SimpleTest('isWebauthnToken', [$this, 'isWebauthnToken']),
            new \Twig_SimpleTest('isRememberMeToken', [$this, 'isRememberMeToken']),
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
}
