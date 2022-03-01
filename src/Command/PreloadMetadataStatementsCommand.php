<?php

declare(strict_types=1);

namespace App\Command;

use App\Repository\MetadataStatementRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class PreloadMetadataStatementsCommand extends Command
{
    protected static $defaultName = 'app:mds:preload';

    public function __construct(
        private MetadataStatementRepository $metadataStatementRepository
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->metadataStatementRepository->warmUp();

        return self::SUCCESS;
    }
}
