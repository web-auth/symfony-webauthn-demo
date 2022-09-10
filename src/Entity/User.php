<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\PublicKeyCredentialUserEntityRepository;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Table(name: 'users')]
#[ORM\Entity(repositoryClass: PublicKeyCredentialUserEntityRepository::class)]
#[UniqueEntity('username')]
class User implements UserInterface
{
    /**
     * @param string[] $roles
     */
    public function __construct(
        #[ORM\Id]
        #[ORM\GeneratedValue(strategy: 'NONE')]
        #[ORM\Column(type: Types::STRING)]
        private string $id,
        #[ORM\Column(type: Types::STRING)]
        private string $username,
        #[ORM\Column(type: Types::STRING)]
        private string $displayName,
        #[ORM\Column(type: Types::ARRAY)]
        private array $roles = []
    ) {
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getUserIdentifier(): string
    {
        return $this->username;
    }

    public function getRoles(): array
    {
        return array_unique($this->roles + ['ROLE_USER']);
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): void
    {
        $this->username = $username;
    }

    public function eraseCredentials(): void
    {
    }

    public function getDisplayName(): string
    {
        return $this->displayName;
    }

    public function setDisplayName(string $displayName): void
    {
        $this->displayName = $displayName;
    }

    public function setLastLoginAt(DateTimeImmutable $last_login_at): void
    {
        $this->last_login_at = $last_login_at;
    }
}
