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
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\ProgressBar;
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

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $progressBar = new ProgressBar($output);
        $progressBar->start();
        $singleStatements = $this->metadataStatementRepository->getSingleStatements();
        $progressBar->setMaxSteps(count($singleStatements));
        foreach ($singleStatements as $name => $singleStatement) {
            try {
                $statement = $singleStatement->getMetadataStatement();
                if ($statement->getAaguid()) {
                    if ($this->filesystemStorage->has($statement->getAaguid())) {
                        $this->filesystemStorage->delete($statement->getAaguid());
                    }
                    $this->filesystemStorage->put(
                        $statement->getAaguid(),
                        json_encode($statement, JSON_UNESCAPED_SLASHES)
                    );
                }
            } catch (Throwable $throwable) {
                $progressBar->advance();
                $this->errors[] = sprintf('Unable to store single statement "%s". Error is: %s. Data is: %s', $name, $throwable->getMessage(), json_encode($statement));
            }
            $progressBar->advance();
        }
        foreach ($this->metadataStatementRepository->getServices() as $name => $service) {
            try {
                $toc = $service->getMetadataTOCPayload();
                $progressBar->setMaxSteps(
                    count($toc->getEntries()) + $progressBar->getMaxSteps()
                );
                foreach ($toc->getEntries() as $entry) {
                    if ($entry->getAaguid()) {
                        if ($this->filesystemStorage->has($entry->getAaguid())) {
                            $this->filesystemStorage->delete($entry->getAaguid());
                        }
                        try {
                            $statement = $service->getMetadataStatementFor($entry);
                            $this->filesystemStorage->put(
                                $statement->getAaguid(),
                                json_encode($statement, JSON_UNESCAPED_SLASHES)
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

        if (0 !== count($this->errors)) {
            $output->writeln('---ERRORS---');
            foreach ($this->errors as $error) {
                $output->writeln($error);
            }
        }
    }
}
