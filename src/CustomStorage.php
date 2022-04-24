<?php

declare(strict_types=1);

namespace App;

use Psr\Cache\CacheItemPoolInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Webauthn\Bundle\Security\Storage\Item;
use Webauthn\Bundle\Security\Storage\OptionsStorage;

final class CustomStorage implements OptionsStorage
{
    private const KEY = 'webauthn-options';

    public function __construct(
        private readonly CacheItemPoolInterface $cacheItemPool
    ) {
    }

    public function store(Item $item): void
    {
        $cache = $this->cacheItemPool->getItem(self::KEY);
        $cache->set($item);
        $this->cacheItemPool->save($cache);
    }

    public function get(): Item
    {
        $cache = $this->cacheItemPool->getItem(self::KEY);
        if (! $cache->isHit()) {
            throw new BadRequestHttpException('No public key credential options available for this session.');
        }

        return $cache->get();
    }
}
