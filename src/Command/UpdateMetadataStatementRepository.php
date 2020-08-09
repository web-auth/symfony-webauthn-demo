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

namespace App\Command;

use App\Repository\MetadataStatementRepository;
use League\Flysystem\FilesystemInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Throwable;

final class UpdateMetadataStatementRepository extends Command
{
    protected static $defaultName = 'app:update-mds';

    /**
     * @var MetadataStatementRepository
     */
    private $metadataStatementRepository;

    /**
     * @var FilesystemInterface
     */
    private $filesystemStorage;

    /**
     * @var string[]
     */
    private $errors = [];

    public function __construct(FilesystemInterface $defaultStorage, MetadataStatementRepository $metadataStatementRepository)
    {
        parent::__construct();
        $this->metadataStatementRepository = $metadataStatementRepository;
        $this->filesystemStorage = $defaultStorage;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $progressBar = new ProgressBar($output);
        $progressBar->start();
        foreach ($this->metadataStatementRepository->getServices() as $name => $service) {
            try {
                $toc = $service->getMetadataTOCPayload();
                if ($this->filesystemStorage->has(sprintf('/toc/%s', $name))) {
                    $this->filesystemStorage->delete(sprintf('/toc/%s', $name));
                }
                $this->filesystemStorage->put(
                    sprintf('/toc/%s', $name),
                    json_encode($toc, JSON_UNESCAPED_SLASHES)
                );
                $progressBar->setMaxSteps(
                    \count($toc->getEntries()) + $progressBar->getMaxSteps()
                );
                foreach ($toc->getEntries() as $entry) {
                    if ($entry->getAaguid()) {
                        if ($this->filesystemStorage->has(sprintf('/mds/%s', $entry->getAaguid()))) {
                            $this->filesystemStorage->delete(sprintf('/mds/%s', $entry->getAaguid()));
                        }
                        if ($this->filesystemStorage->has(sprintf('/entries/%s', $entry->getAaguid()))) {
                            $this->filesystemStorage->delete(sprintf('/entries/%s', $entry->getAaguid()));
                        }
                        try {
                            $statement = $service->getMetadataStatementFor($entry);
                            $this->filesystemStorage->put(
                                sprintf('/mds/%s', $statement->getAaguid()),
                                json_encode($statement, JSON_UNESCAPED_SLASHES)
                            );
                            $this->filesystemStorage->put(
                                sprintf('/entries/%s', $statement->getAaguid()),
                                json_encode($entry, JSON_UNESCAPED_SLASHES)
                            );
                            $progressBar->advance();
                        } catch (Throwable $throwable) {
                            $progressBar->advance();
                            $this->errors[] = sprintf('Unable to store statement "%s" from service "%s". Error is: %s. Entry is: %s. Statement is: %s', $entry->getAaguid(), $name, $throwable->getMessage(), json_encode($entry), json_encode($statement));
                        }
                    }
                }
            } catch (Throwable $throwable) {
                $this->errors[] = sprintf('Unable to fetch data from service "%s". Error is: %s', $name, $throwable->getMessage());
            }
        }
        $progressBar->finish();

        if (0 !== \count($this->errors)) {
            $output->writeln('---ERRORS---');
            foreach ($this->errors as $error) {
                $output->writeln($error);
            }
        }

        return 0;
    }
}
