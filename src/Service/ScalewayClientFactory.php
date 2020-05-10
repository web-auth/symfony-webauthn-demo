<?php

declare(strict_types=1);

/*
 * The MIT License (MIT)
 *
 * Copyright (c) 2014-2019 Spomky-Labs
 *
 * This software may be modified and distributed under the terms
 * of the MIT license.  See the LICENSE file for details.
 */

namespace App\Service;

use Aws\S3\S3Client;

class ScalewayClientFactory
{
    public function create(string $key, string $secret): S3Client
    {
        return new S3Client([
            'endpoint' => 'https://s3.fr-par.scw.cloud',
            'region' => 'fr-par',
            'version' => 'latest',
            'credentials' => [
                'key' => $key,
                'secret' => $secret,
            ],
        ]);
    }
}
