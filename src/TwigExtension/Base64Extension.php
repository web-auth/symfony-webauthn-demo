<?php

/*
 * This file is part of the appname project.
 *
 * (c) Romain Gautier <mail@romain.sh>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\TwigExtension;

use Twig\TwigFilter;

class Base64Extension extends \Twig_Extension
{
    public function getFilters()
    {
        return [
            new TwigFilter('base64_encode', [$this, 'encode']),
            new TwigFilter('base64_decode', [$this, 'decode']),
        ];
    }

    public function encode($input)
    {
        return base64_encode($input);
    }

    public function decode($input)
    {
        return base64_decode($input, true);
    }
}
