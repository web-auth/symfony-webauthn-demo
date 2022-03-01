<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\PublicKeyCredentialUserEntityRepository;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Webauthn\PublicKeyCredentialUserEntity;

#[ORM\Table(name: 'users')]
#[ORM\Entity(repositoryClass: PublicKeyCredentialUserEntityRepository::class)]
#[UniqueEntity('name')]
class User extends PublicKeyCredentialUserEntity implements UserInterface
{
    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private DateTimeImmutable $created_at;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?DateTimeImmutable $last_login_at = null;

    /**
     * @param string[] $roles
     */
    public function __construct(
        string $name,
        string $displayName,
        #[ORM\Column(type: Types::ARRAY)] protected array $roles = [],
        ?string $icon = null
    ) {
        $this->id = Uuid::uuid4()->toString();
        parent::__construct($name, $this->id, $displayName, $icon);
        $this->created_at = new DateTimeImmutable();
    }

    public function getRoles(): array
    {
        return array_unique($this->roles + ['ROLE_USER']);
    }

    public function getPassword(): void
    {
    }

    public function getSalt(): void
    {
    }

    public function getUsername(): ?string
    {
        return $this->getName();
    }

    public function eraseCredentials(): void
    {
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->created_at;
    }

    public function getLastLoginAt(): ?DateTimeImmutable
    {
        return $this->last_login_at;
    }

    public function setDisplayName(string $displayName): void
    {
        $this->displayName = $displayName;
    }

    public function setLastLoginAt(DateTimeImmutable $last_login_at): void
    {
        $this->last_login_at = $last_login_at;
    }

    public static function createFrom(PublicKeyCredentialUserEntity $userEntity): self
    {
        $user = new self($userEntity->getName(), $userEntity->getDisplayName(), [], $userEntity->getIcon());
        $user->id = $userEntity->getId();

        return $user;
    }
}
