<?php

declare(strict_types=1);

/*
 * The MIT License (MIT)
 *
 * Copyright (c) 2014-2020 Spomky-Labs
 *
 * This software may be modified and distributed under the terms
 * of the MIT license.  See the LICENSE file for details.
 */

namespace App\Command;

use App\Repository\MetadataStatementRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class PreloadMetadataStatementsCommand extends Command
{
    protected static $defaultName = 'app:mds:preload';

    /**
     * @var MetadataStatementRepository
     */
    private $metadataStatementRepository;

    public function __construct(MetadataStatementRepository $metadataStatementRepository)
    {
        parent::__construct();
        $this->metadataStatementRepository = $metadataStatementRepository;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->metadataStatementRepository->warmUp();

        return 0;
    }
}
