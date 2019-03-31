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

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Webauthn\PublicKeyCredentialUserEntity;
use Webauthn\SecurityBundle\Model\CanHaveRegisteredSecurityDevices;
use Webauthn\SecurityBundle\Model\HasUserHandle;

/**
 * @ORM\Table(name="users")
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @UniqueEntity("name")
 */
class User extends PublicKeyCredentialUserEntity implements UserInterface, CanHaveRegisteredSecurityDevices, HasUserHandle
{
    /**
     * @ORM\Id
     * @ORM\Column(type="string", length=255)
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Length(max = 100)
     */
    protected $name;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Length(max = 100)
     */
    protected $displayName;

    /**
     * @ORM\Column(type="array")
     */
    protected $roles;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    protected $created_at;

    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    protected $last_login_at = null;

    /**
     * @var PublicKeyCredentialSource[]
     * @ORM\ManyToMany(targetEntity="App\Entity\PublicKeyCredentialSource")
     * @ORM\JoinTable(name="users_user_handles",
     *      joinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="user_handle", referencedColumnName="id", unique=true)}
     *      )
     */
    protected $publicKeyCredentialSources;

    public function __construct(string $id, string $name, string $displayName, array $roles)
    {
        parent::__construct($name, $id, $displayName);
        $this->roles = $roles;
        $this->publicKeyCredentialSources = new ArrayCollection();
        $this->created_at = new \DateTimeImmutable();
    }

    public function getSecurityDeviceCredentialIds(): iterable
    {
        $publicKeyCredentialDescriptors = [];
        foreach ($this->publicKeyCredentialSources as $credential) {
            $publicKeyCredentialDescriptors[] = $credential->getPublicKeyCredentialDescriptor();
        }

        yield from $publicKeyCredentialDescriptors;
    }

    /**
     * @return PublicKeyCredentialSource[]
     */
    public function getPublicKeyCredentialSources(): array
    {
        return $this->publicKeyCredentialSources->getValues();
    }

    public function addPublicKeyCredentialSource(PublicKeyCredentialSource $credential): void
    {
        $this->publicKeyCredentialSources->add($credential);
    }

    public function removePublicKeyCredentialSource(PublicKeyCredentialSource $credential): void
    {
        $this->publicKeyCredentialSources->removeElement($credential);
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
        return $this->name;
    }

    public function eraseCredentials(): void
    {
    }

    public function getUserHandle(): string
    {
        return $this->getId();
    }

    public function getCreatedAt(): \DateTimeInterface
    {
        return $this->created_at;
    }

    public function getLastLoginAt(): \DateTimeInterface
    {
        return $this->last_login_at;
    }

    /**
     * @param \DateTimeInterface $last_login_at
     */
    public function setLastLoginAt(\DateTimeInterface $last_login_at): void
    {
        $this->last_login_at = $last_login_at;
    }
}
