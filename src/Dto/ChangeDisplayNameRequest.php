<?php

declare(strict_types=1);

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

final class ChangeDisplayNameRequest
{
    #[Assert\Type('string')]
    #[Assert\NotBlank]
    public string $displayName;
}
