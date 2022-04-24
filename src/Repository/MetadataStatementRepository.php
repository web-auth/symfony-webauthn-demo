<?php

declare(strict_types=1);

namespace App\Repository;

use Psr\Cache\CacheItemPoolInterface;
use Webauthn\MetadataService\MetadataStatementRepository as MetadataStatementRepositoryInterface;
use Webauthn\MetadataService\Statement\MetadataStatement;

final class MetadataStatementRepository implements MetadataStatementRepositoryInterface
{
    public function __construct(
        private readonly CacheItemPoolInterface $cacheItemPool,
    ) {
    }

    public function findOneByAAGUID(string $aaguid): ?MetadataStatement
    {
        $item = $this->cacheItemPool->getItem(sprintf('mds-%s', $aaguid));
        if (! $item->isHit()) {
            return null;
        }

        return $item->get();
    }

    public function save(MetadataStatement $mds): void
    {
        $item = $this->cacheItemPool->getItem(sprintf('mds-%s', $mds->getAaguid()));
        $item->set($mds);
        $this->cacheItemPool->save($item);
    }
}
