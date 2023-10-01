<?php

declare(strict_types=1);

namespace App\Command;

use App\Repository\PublicKeyCredentialSourceRepository;
use App\Repository\UserRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(name: 'app:cleanup', description: 'Remove old users and authenticators')]
class CleanupCommand extends Command
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly PublicKeyCredentialSourceRepository $publicKeyCredentialSourceRepository,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $users = $this->userRepository->findAllInactive();
        foreach ($users as $user) {
            $this->publicKeyCredentialSourceRepository->removeAllOfUser($user);
            $this->userRepository->remove($user);
        }

        $io->success('All inactive users and credentials are now removed from the database.');

        return Command::SUCCESS;
    }
}
