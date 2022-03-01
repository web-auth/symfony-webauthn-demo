<?php

declare(strict_types=1);

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;
use Webauthn\PublicKeyCredentialCreationOptions;

final class ServerPublicKeyCredentialCreationOptionsRequest
{
    /**
     * @var string
     */
    #[Assert\Type('string')]
    #[Assert\NotBlank]
    public string $username;

    /**
     * @var string
     */
    #[Assert\Type('string')]
    #[Assert\NotBlank]
    public string $displayName;

    public ?array $authenticatorSelection;

    /**
     * @var string
     */
    #[Assert\Type('string')]
    #[Assert\Choice([
        PublicKeyCredentialCreationOptions::ATTESTATION_CONVEYANCE_PREFERENCE_NONE,
        PublicKeyCredentialCreationOptions::ATTESTATION_CONVEYANCE_PREFERENCE_DIRECT,
        PublicKeyCredentialCreationOptions::ATTESTATION_CONVEYANCE_PREFERENCE_INDIRECT,
    ])]
    public string $attestation;

    public ?array $extensions;
}
