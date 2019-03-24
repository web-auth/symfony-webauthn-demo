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
use Webauthn\PublicKeyCredentialCreationOptions;

final class ServerPublicKeyCredentialCreationOptionsRequest
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
     * @Assert\NotBlank
     */
    public $displayName;

    /**
     * @var array|null
     */
    public $authenticatorSelection;

    /**
     * @var string
     *
     * @Assert\Type("string")
     * @Assert\Choice({PublicKeyCredentialCreationOptions::ATTESTATION_CONVEYANCE_PREFERENCE_NONE, PublicKeyCredentialCreationOptions::ATTESTATION_CONVEYANCE_PREFERENCE_DIRECT, PublicKeyCredentialCreationOptions::ATTESTATION_CONVEYANCE_PREFERENCE_INDIRECT})
     */
    public $attestation;
}
