<?php

declare(strict_types=1);

namespace App\Entity;

use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Webauthn\PublicKeyCredentialUserEntity;

/**
 * @ORM\Table(name="users")
 * @ORM\Entity(repositoryClass="App\Repository\PublicKeyCredentialUserEntityRepository")
 * @UniqueEntity("name")
 */
class User extends PublicKeyCredentialUserEntity implements UserInterface
{
    /**
     * @var string
     *
     * @ORM\Id
     * @ORM\Column(type="string")
     * @ORM\GeneratedValue(strategy="NONE")
     */
    protected $id;

    /**
     * @var string[]
     *
     * @ORM\Column(type="array")
     */
    protected $roles;

    /**
     * @var DateTimeImmutable
     *
     * @ORM\Column(type="datetime_immutable")
     */
    private $created_at;

    /**
     * @var DateTimeImmutable|null
     *
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private $last_login_at;

    public function __construct(string $name, string $displayName, array $roles = [], ?string $icon = null)
    {
        $this->id = Uuid::uuid4()->toString();
        parent::__construct($name, $this->id, $displayName, $icon);
        $this->roles = $roles;
        $this->created_at = new DateTimeImmutable();
    }

    public function getId(): string
    {
        return parent::getId();
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

    public function setLastLoginAt(DateTimeImmutable $last_login_at): void
    {
        $this->last_login_at = $last_login_at;
    }

    public static function createFrom(PublicKeyCredentialUserEntity $userEntity): User
    {
        $user = new self(
            $userEntity->getName(),
            $userEntity->getDisplayName(),
            [],
            $userEntity->getIcon()
        );
        $user->id = $userEntity->getId();

        return $user;
    }
}
