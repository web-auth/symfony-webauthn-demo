<?php

declare(strict_types=1);

namespace App\Repository;

use Psr\Cache\CacheItemPoolInterface;
use Webauthn\MetadataService\Statement\StatusReport;
use Webauthn\MetadataService\StatusReportRepository as StatusReportRepositoryInterface;

final class StatusReportRepository implements StatusReportRepositoryInterface
{
    public function __construct(
        private readonly CacheItemPoolInterface $cacheItemPool,
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function findStatusReportsByAAGUID(string $aaguid): array
    {
        $item = $this->cacheItemPool->getItem(sprintf('sr-%s', $aaguid));
        if (! $item->isHit()) {
            return [];
        }

        return $item->get();
    }

    /**
     * @param StatusReport[] $statusReports
     */
    public function save(mixed $aaguid, iterable $statusReports)
    {
        $item = $this->cacheItemPool->getItem(sprintf('sr-%s', $aaguid));
        $item->set($statusReports);
        $this->cacheItemPool->save($item);
    }
}
