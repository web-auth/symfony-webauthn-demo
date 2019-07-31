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

namespace App\Repository;

use Base64Url\Base64Url;
use Psr\Cache\CacheItemPoolInterface;
use Webauthn\PublicKeyCredentialSource;
use Webauthn\PublicKeyCredentialSourceRepository as PublicKeyCredentialSourceRepositoryInterface;
use Webauthn\PublicKeyCredentialUserEntity;

final class ConformancePublicKeyCredentialSourceRepository implements PublicKeyCredentialSourceRepositoryInterface
{
    private $cacheItemPool;

    public function __construct(CacheItemPoolInterface $cacheItemPool)
    {
        $this->cacheItemPool = $cacheItemPool;
    }

    public function findOneByCredentialId(string $publicKeyCredentialId): ?PublicKeyCredentialSource
    {
        $item = $this->cacheItemPool->getItem('pks-'.Base64Url::encode($publicKeyCredentialId));
        if (!$item->isHit()) {
            return null;
        }

        return $item->get();
    }

    public function findAllForUserEntity(PublicKeyCredentialUserEntity $publicKeyCredentialUserEntity): array
    {
        $item = $this->cacheItemPool->getItem('user-pks-'.Base64Url::encode($publicKeyCredentialUserEntity->getId()));
        if (!$item->isHit()) {
            return [];
        }

        return $item->get();
    }

    public function saveCredentialSource(PublicKeyCredentialSource $publicKeyCredentialSource): void
    {
        $item = $this->cacheItemPool->getItem('pks-'.Base64Url::encode($publicKeyCredentialSource->getPublicKeyCredentialId()));
        $item->set($publicKeyCredentialSource);
        $this->cacheItemPool->save($item);

        $item = $this->cacheItemPool->getItem('user-pks-'.Base64Url::encode($publicKeyCredentialSource->getUserHandle()));
        $pks = [];
        if ($item->isHit()) {
            $pks = $item->get();
        }
        $pks[] = $publicKeyCredentialSource;
        $item->set($pks);
        $this->cacheItemPool->save($item);
    }
}
