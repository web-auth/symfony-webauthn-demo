<?php

declare(strict_types=1);

/*
 * This file is part of the Webauthn Demo project.
 *
 * (c) Florent Morselli
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Command;

use App\Entity\Credential;
use App\Entity\PublicKeyCredentialSource;
use App\Repository\CredentialRepository;
use App\Repository\PublicKeyCredentialSourceRepository;
use App\Repository\UserRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Webauthn\PublicKeyCredentialDescriptor;
use Webauthn\TrustPath\EmptyTrustPath;

class MigrationCommand extends Command
{
    protected static $defaultName = 'webauthn:migration';
    /**
     * @var CredentialRepository|null
     */
    private $credentialRepository;

    /**
     * @var PublicKeyCredentialSourceRepository|null
     */
    private $publicKeyCredentialSourceRepository;

    /**
     * @var UserRepository|null
     */
    private $userRepository;

    public function __construct(?UserRepository $userRepository, ?CredentialRepository $credentialRepository, ?PublicKeyCredentialSourceRepository $publicKeyCredentialSourceRepository)
    {
        parent::__construct();
        $this->credentialRepository = $credentialRepository;
        $this->publicKeyCredentialSourceRepository = $publicKeyCredentialSourceRepository;
        $this->userRepository = $userRepository;
    }

    public function isEnabled(): bool
    {
        return null !== $this->credentialRepository && null !== $this->publicKeyCredentialSourceRepository && null !== $this->userRepository;
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Migrates Credentials to Public Key Credential Sources.')
            ->setHelp('This command migrates all "Credential" objects to the new "PublicKeyCredentialSource" objects.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('*** Migration from the old repository to the new one ***');
        /** @var Credential[] $credentials */
        $credentials = $this->credentialRepository->all();
        if (\count($credentials) > 0) {
            $this->upgrade($credentials, $output);
            $output->writeln('*** Done ***');
        } else {
            $output->writeln('*** No credential to migrate ***');
        }
    }

    private function upgrade(array $credentials, OutputInterface $output): void
    {
        $progressBar = new ProgressBar($output, \count($credentials));
        $progressBar->start();
        foreach ($credentials as $credential) {
            $user = $this->userRepository->findOneById($credential->getUserHandle());
            if (null === $user) {
                continue;
            }

            $publicKeyCredentialSource = new PublicKeyCredentialSource(
                $credential->getId(),
                PublicKeyCredentialDescriptor::CREDENTIAL_TYPE_PUBLIC_KEY,
                [],
                'none',
                new EmptyTrustPath(),
                $credential->getAttestedCredentialData()->getAaguid(),
                $credential->getAttestedCredentialData()->getCredentialPublicKey(),
                $credential->getUserHandle(),
                $credential->getCounter()
            );
            $user->addPublicKeyCredentialSource($publicKeyCredentialSource);
            $this->publicKeyCredentialSourceRepository->save($publicKeyCredentialSource);
            $this->userRepository->save($user);
            $progressBar->advance();
        }
        $progressBar->finish();
    }
}
