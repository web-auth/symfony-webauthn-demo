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

use Ramsey\Uuid\Uuid;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class UuidExtension extends AbstractExtension
{
    public function getFilters()
    {
        return [
            new TwigFilter('uuid', [$this, 'encode']),
        ];
    }

    public function encode($input)
    {
        return Uuid::fromBytes($input)->toString();
    }
}
