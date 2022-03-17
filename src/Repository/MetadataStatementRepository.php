<?php

declare(strict_types=1);

namespace App\Repository;

use Webauthn\AttestationStatement\CanSupportStatusReport;
use Webauthn\MetadataService\CanSupportImport;
use Webauthn\MetadataService\MetadataStatementRepository as MetadataStatementRepositoryInterface;
use Webauthn\MetadataService\Service\MetadataService;
use Webauthn\MetadataService\Statement\MetadataStatement;

final class MetadataStatementRepository implements MetadataStatementRepositoryInterface, CanSupportStatusReport, CanSupportImport
{
    public function __construct(
        private MetadataService $service
    ) {
    }

    public function findOneByAAGUID(string $aaguid): ?MetadataStatement
    {
        if (! $this->service->has($aaguid)) {
            return null;
        }

        return $this->service->get($aaguid);
    }

    /**
     * {@inheritdoc}
     */
    public function findStatusReportsByAAGUID(string $aaguid): array
    {
        return [];
    }

    public function import(MetadataStatement $metadataStatement): void
    {
        dd($metadataStatement);
    }
}
