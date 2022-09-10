<?php

declare(strict_types=1);

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

final class Base64Extension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [new TwigFilter('toBase64', 'base64_encode'), new TwigFilter('toHex', 'bin2hex')];
    }
}
