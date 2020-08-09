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
use Webauthn\MetadataService\Object\SingleMetadata;

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

    /**
     * @var MetadataStatementInterface[]
     */
    private $singleStatements;

    public function __construct(ClientInterface $client, RequestFactoryInterface $requestFactory)
    {
        $this->client = $client;
        $this->requestFactory = $requestFactory;

        $this->addSingleStatements();
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
        if (isset($this->singleStatements[$aaguid])) {
            return $this->singleStatements[$aaguid];
        }

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

    private function addSingleStatements(): void
    {
        $statements = [
            'yubico' => '{"description": "Yubico U2F Root CA Serial 457200631","aaguid": "f8a011f3-8c0a-4d15-8006-17111f9edc7d","protocolFamily": "fido2","attestationRootCertificates": ["MIIDHjCCAgagAwIBAgIEG0BT9zANBgkqhkiG9w0BAQsFADAuMSwwKgYDVQQDEyNZdWJpY28gVTJGIFJvb3QgQ0EgU2VyaWFsIDQ1NzIwMDYzMTAgFw0xNDA4MDEwMDAwMDBaGA8yMDUwMDkwNDAwMDAwMFowLjEsMCoGA1UEAxMjWXViaWNvIFUyRiBSb290IENBIFNlcmlhbCA0NTcyMDA2MzEwggEiMA0GCSqGSIb3DQEBAQUAA4IBDwAwggEKAoIBAQC/jwYuhBVlqaiYWEMsrWFisgJ+PtM91eSrpI4TK7U53mwCIawSDHy8vUmk5N2KAj9abvT9NP5SMS1hQi3usxoYGonXQgfO6ZXyUA9a+KAkqdFnBnlyugSeCOep8EdZFfsaRFtMjkwz5Gcz2Py4vIYvCdMHPtwaz0bVuzneueIEz6TnQjE63Rdt2zbwnebwTG5ZybeWSwbzy+BJ34ZHcUhPAY89yJQXuE0IzMZFcEBbPNRbWECRKgjq//qT9nmDOFVlSRCt2wiqPSzluwn+v+suQEBsUjTGMEd25tKXXTkNW21wIWbxeSyUoTXwLvGS6xlwQSgNpk2qXYwf8iXg7VWZAgMBAAGjQjBAMB0GA1UdDgQWBBQgIvz0bNGJhjgpToksyKpP9xv9oDAPBgNVHRMECDAGAQH/AgEAMA4GA1UdDwEB/wQEAwIBBjANBgkqhkiG9w0BAQsFAAOCAQEAjvjuOMDSa+JXFCLyBKsycXtBVZsJ4Ue3LbaEsPY4MYN/hIQ5ZM5p7EjfcnMG4CtYkNsfNHc0AhBLdq45rnT87q/6O3vUEtNMafbhU6kthX7Y+9XFN9NpmYxr+ekVY5xOxi8h9JDIgoMP4VB1uS0aunL1IGqrNooL9mmFnL2kLVVee6/VR6C5+KSTCMCWppMuJIZII2v9o4dkoZ8Y7QRjQlLfYzd3qGtKbw7xaF1UsG/5xUb/Btwb2X2g4InpiB/yt/3CpQXpiWX/K4mBvUKiGn05ZsqeY1gx4g0xLBqcU9psmyPzK+Vsgw2jeRQ5JlKDyqE0hebfC1tvFu0CCrJFcw=="]}',
        ];
        $files = scandir(__DIR__.'/../../mds/');
        foreach ($files as $file) {
            if (is_file(__DIR__.'/../../mds/'.$file)) {
                $statements[hash('sha256', $file)] = file_get_contents(__DIR__.'/../../mds/'.$file);
            }
        }

        foreach ($statements as $name => $statement) {
            $mds = (new SingleMetadata($statement, false))->getMetadataStatement();
            $this->singleStatements[$mds->getAaguid()] = $mds;
        }
    }
}
