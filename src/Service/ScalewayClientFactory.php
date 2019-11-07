<?php

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
                'key'    => $key,
                'secret' => $secret,
            ],
        ]);
    }
}
