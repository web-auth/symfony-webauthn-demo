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

use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Webauthn\MetadataService\MetadataStatementInterface;
use Throwable;
use Webauthn\MetadataService\Object\MetadataService;
use Webauthn\MetadataService\MetadataStatementRepository as MetadataStatementRepositoryInterface;

final class MetadataStatementRepository implements MetadataStatementRepositoryInterface
{
    /**
     * @var MetadataService[]
     */
    private $services = [];

    /**
     * @var ClientInterface
     */
    private $client;

    /**
     * @var RequestFactoryInterface
     */
    private $requestFactory;

    public function __construct(ClientInterface $client, RequestFactoryInterface $requestFactory)
    {
        $this->client = $client;
        $this->requestFactory = $requestFactory;
    }

    public function addService(string $name, string $url, array $additional_query_strings = [], $additional_headers = []): void
    {
        $this->services[$name] = new MetadataService(
            $url,
            $this->client,
            $this->requestFactory,
            $additional_query_strings,
            $additional_headers
        );
    }

    public function findOneByAAGUID(string $aaguid): ?MetadataStatementInterface
    {
        foreach ($this->services as $name => $service) {
            try {
                $toc = $service->getMetadataTOCPayload();
                foreach ($toc->getEntries() as $entry) {
                    if ($entry->getAaguid()) {
                        try {
                            return $service->getMetadataStatementFor($entry);
                        } catch (Throwable $throwable) {
                            continue;
                        }
                    }
                }
            } catch (Throwable $throwable) {
                continue;
            }
        }

        return null;
    }

    public function findStatusReportsByAAGUID(string $aaguid): array
    {
        foreach ($this->services as $name => $service) {
            try {
                $toc = $service->getMetadataTOCPayload();
                foreach ($toc->getEntries() as $entry) {
                    if ($entry->getAaguid()) {
                        try {
                            return $entry->getStatusReports();
                        } catch (Throwable $throwable) {
                            continue;
                        }
                    }
                }
            } catch (Throwable $throwable) {
                continue;
            }
        }

        return [];
    }
}
