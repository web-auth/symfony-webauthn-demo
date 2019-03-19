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
use Symfony\Component\Validator\Constraints as Assert;
use Webauthn\PublicKeyCredentialDescriptorCollection;
use Webauthn\PublicKeyCredentialUserEntity;
use Webauthn\SecurityBundle\Model\CanHaveRegisteredSecurityDevices;
use Webauthn\SecurityBundle\Model\HasUserHandle;

/**
 * @ORM\Table(name="users")
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @UniqueEntity("username")
 */
class User extends PublicKeyCredentialUserEntity implements CanHaveRegisteredSecurityDevices, HasUserHandle
{
    /**
     * @ORM\Id
     * @ORM\Column(type="string", length=255)
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Length(max = 100)
     */
    private $username;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Length(max = 100)
     */
    private $displayName;

    /**
     * @ORM\Column(type="array")
     */
    private $roles;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private $created_at;

    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private $last_login_at = null;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Credential", mappedBy="user")
     */
    private $credentials;

    /**
     * @var PublicKeyCredentialSource[]
     * @ORM\ManyToMany(targetEntity="App\Entity\PublicKeyCredentialSource")
     * @ORM\JoinTable(name="users_user_handles",
     *      joinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="user_handle", referencedColumnName="id", unique=true)}
     *      )
     */
    private $publicKeyCredentialSources;

    public function __construct(string $id, string $username, string $displayName, array $roles)
    {
        parent::__construct($username, $id, $displayName);
        $this->id = $id;
        $this->username = $username;
        $this->roles = $roles;
        $this->credentials = new ArrayCollection();
        $this->publicKeyCredentialSources = new ArrayCollection();
        $this->displayName = $displayName;
        $this->created_at = new \DateTimeImmutable();
    }

    public function getId(): string
    {
        return $this->id;
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
     * @return Credential[]
     */
    public function getCredentials(): array
    {
        return $this->credentials->getValues();
    }

    public function removeCredential(Credential $credential): void
    {
        $this->credentials->removeElement($credential);
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

    public function getUserHandle(): string
    {
        return $this->id;
    }

    public function setPublicKeyCredentialDescriptorCollection(PublicKeyCredentialDescriptorCollection $credentials): void
    {
        $this->publicKeyCredentialSources = $credentials;
    }

    public function getRoles(): array
    {
        return array_unique($this->roles + ['ROLE_USER']);
    }

    public function addRole(string $role): void
    {
        if (!\in_array($role, $this->getRoles(), true)) {
            $this->roles[] = $role;
        }
    }

    public function removeRole(string $role): void
    {
        $key = array_search($role, $this->roles, true);
        if (false !== $key) {
            unset($this->roles[$key]);
        }
    }

    public function getPassword()
    {
    }

    public function getSalt()
    {
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): void
    {
        $this->username = $username;
    }

    public function getDisplayName(): string
    {
        return $this->displayName;
    }

    public function setDisplayName(string $displayName): void
    {
        $this->displayName = $displayName;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->created_at;
    }

    public function getLastLoginAt(): ?\DateTimeImmutable
    {
        return $this->last_login_at;
    }

    public function setLastLoginAt(\DateTimeImmutable $last_login_at): void
    {
        $this->last_login_at = $last_login_at;
    }

    public function eraseCredentials()
    {
    }
}
