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

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;
use Webauthn\PublicKeyCredentialRequestOptions;

final class ServerPublicKeyCredentialRequestOptionsRequest
{
    /**
     * @var string
     *
     * @Assert\Type("string")
     * @Assert\NotBlank
     */
    public $username;

    /**
     * @var string
     *
     * @Assert\Type("string")
     * @Assert\Choice({PublicKeyCredentialRequestOptions::USER_VERIFICATION_REQUIREMENT_PREFERRED, PublicKeyCredentialRequestOptions::USER_VERIFICATION_REQUIREMENT_REQUIRED, PublicKeyCredentialRequestOptions::USER_VERIFICATION_REQUIREMENT_DISCOURAGED})
     */
    public $userVerification = PublicKeyCredentialRequestOptions::USER_VERIFICATION_REQUIREMENT_PREFERRED;
}
