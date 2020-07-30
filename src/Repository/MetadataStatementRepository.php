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

use League\Flysystem\FilesystemInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Throwable;
use Webauthn\MetadataService\DistantSingleMetadata;
use Webauthn\MetadataService\MetadataService;
use Webauthn\MetadataService\MetadataStatement;
use Webauthn\MetadataService\MetadataStatementRepository as MetadataStatementRepositoryInterface;
use Webauthn\MetadataService\MetadataTOCPayloadEntry;
use Webauthn\MetadataService\SingleMetadata;
use function Safe\scandir;
use function Safe\file_get_contents;

final class MetadataStatementRepository implements MetadataStatementRepositoryInterface
{
    /**
     * @var FilesystemInterface
     */
    private $filesystemStorage;

    /**
     * @var MetadataService[]
     */
    private $services = [];

    /**
     * @var SingleMetadata[]
     */
    private $singleStatements = [];
    /**
     * @var ClientInterface
     */
    private $client;
    /**
     * @var RequestFactoryInterface
     */
    private $requestFactory;

    public function __construct(FilesystemInterface $defaultStorage, ClientInterface $client, RequestFactoryInterface $requestFactory)
    {
        $this->client = $client;
        $this->filesystemStorage = $defaultStorage;
        $this->requestFactory = $requestFactory;

        $this->addServices();
        $this->addSingleStatements();
        $this->addDistantSingleStatements();
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

    private function addServices(): void
    {
        $services = [
            'fido_alliance_test1' => ['url' => 'https://fidoalliance.co.nz/mds/execute/0246d3ec6b1463f5d63b200ffc3c1d1b802f1cde1b10788e02208902127dbbce'],
            'fido_alliance_test2' => ['url' => 'https://fidoalliance.co.nz/mds/execute/1323cc994c463752fd60e111defe1fe2187ce344925a5f2282acbbcff6d55a0e'],
            'fido_alliance_test3' => ['url' => 'https://fidoalliance.co.nz/mds/execute/309a94da23dc596fe4e4c2e2528bfaecc3cd95bf0ce2a369eb29a72b08dd680c'],
            'fido_alliance_test4' => ['url' => 'https://fidoalliance.co.nz/mds/execute/b1d139dbe31d0fc2be43b0584c555f9a3b0548ed397b7dbb123875b4c2335e7b'],
            'fido_alliance_test5' => ['url' => 'https://fidoalliance.co.nz/mds/execute/ce2d088e0e35c6196c9e158b09313453f32a44e7e59fb554ac266e33efdd86e5'],
        ];
        foreach ($services as $name => $service) {
            $this->services[$name] = new MetadataService(
                $service['url'],
                $this->client,
                $this->requestFactory,
                $service['additional_query_strings'] ?? [],
                $service['additional_headers'] ?? []
            );
        }
    }

    private function addDistantSingleStatements(): void
    {
        $urls = [
            'solo' => 'https://raw.githubusercontent.com/solokeys/solo/2.1.0/metadata/Solo-FIDO2-CTAP2-Authenticator.json',
        ];

        foreach ($urls as $name => $url) {
            $this->singleStatements[$name] = new DistantSingleMetadata($url, false, $this->client, $this->requestFactory);
        }
    }

    private function addSingleStatements(): void
    {
        $statements = [
            'yubico' => '{"description": "Yubico U2F Root CA Serial 457200631","aaguid": "f8a011f3-8c0a-4d15-8006-17111f9edc7d","protocolFamily": "fido2","attestationRootCertificates": ["MIIDHjCCAgagAwIBAgIEG0BT9zANBgkqhkiG9w0BAQsFADAuMSwwKgYDVQQDEyNZdWJpY28gVTJGIFJvb3QgQ0EgU2VyaWFsIDQ1NzIwMDYzMTAgFw0xNDA4MDEwMDAwMDBaGA8yMDUwMDkwNDAwMDAwMFowLjEsMCoGA1UEAxMjWXViaWNvIFUyRiBSb290IENBIFNlcmlhbCA0NTcyMDA2MzEwggEiMA0GCSqGSIb3DQEBAQUAA4IBDwAwggEKAoIBAQC/jwYuhBVlqaiYWEMsrWFisgJ+PtM91eSrpI4TK7U53mwCIawSDHy8vUmk5N2KAj9abvT9NP5SMS1hQi3usxoYGonXQgfO6ZXyUA9a+KAkqdFnBnlyugSeCOep8EdZFfsaRFtMjkwz5Gcz2Py4vIYvCdMHPtwaz0bVuzneueIEz6TnQjE63Rdt2zbwnebwTG5ZybeWSwbzy+BJ34ZHcUhPAY89yJQXuE0IzMZFcEBbPNRbWECRKgjq//qT9nmDOFVlSRCt2wiqPSzluwn+v+suQEBsUjTGMEd25tKXXTkNW21wIWbxeSyUoTXwLvGS6xlwQSgNpk2qXYwf8iXg7VWZAgMBAAGjQjBAMB0GA1UdDgQWBBQgIvz0bNGJhjgpToksyKpP9xv9oDAPBgNVHRMECDAGAQH/AgEAMA4GA1UdDwEB/wQEAwIBBjANBgkqhkiG9w0BAQsFAAOCAQEAjvjuOMDSa+JXFCLyBKsycXtBVZsJ4Ue3LbaEsPY4MYN/hIQ5ZM5p7EjfcnMG4CtYkNsfNHc0AhBLdq45rnT87q/6O3vUEtNMafbhU6kthX7Y+9XFN9NpmYxr+ekVY5xOxi8h9JDIgoMP4VB1uS0aunL1IGqrNooL9mmFnL2kLVVee6/VR6C5+KSTCMCWppMuJIZII2v9o4dkoZ8Y7QRjQlLfYzd3qGtKbw7xaF1UsG/5xUb/Btwb2X2g4InpiB/yt/3CpQXpiWX/K4mBvUKiGn05ZsqeY1gx4g0xLBqcU9psmyPzK+Vsgw2jeRQ5JlKDyqE0hebfC1tvFu0CCrJFcw=="]}',
        ];
        $files = scandir(__DIR__.'/../../mds/');
        foreach($files as $file) {
            if (is_file(__DIR__.'/../../mds/'.$file)) {
                $statements[hash('sha256', $file)] = file_get_contents(__DIR__.'/../../mds/'.$file);
            }
        }

        foreach ($statements as $name => $statement) {
            $this->singleStatements[$name] = new SingleMetadata($statement, false);
        }
        dump($this->singleStatements);
    }

    /**
     * @return MetadataService[]
     */
    public function getServices(): array
    {
        return $this->services;
    }

    /**
     * @return SingleMetadata[]
     */
    public function getSingleStatements(): array
    {
        return $this->singleStatements;
    }

    public function findOneByAAGUID(string $aaguid): ?MetadataStatement
    {
        try {
            $data = $this->filesystemStorage->read(sprintf('/mds/%s', $aaguid));
            if (false === $data) {
                return null;
            }
            $json = json_decode($data, true);
            if (!\is_array($json)) {
                return null;
            }

            return MetadataStatement::createFromArray($json);
        } catch (Throwable $throwable) {
            return null;
        }
    }

    public function findStatusReportsByAAGUID(string $aaguid): array
    {
        try {
            $data = $this->filesystemStorage->read(sprintf('/entries/%s', $aaguid));
            if (false === $data) {
                return [];
            }
            $json = json_decode($data, true);
            if (!\is_array($json)) {
                return [];
            }

            return MetadataTOCPayloadEntry::createFromArray($json)->getStatusReports();
        } catch (Throwable $throwable) {
            return [];
        }
    }
}
