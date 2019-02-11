<?php

declare(strict_types=1);

/*
 * This file is part of the appname project.
 *
 * (c) Romain Gautier <mail@romain.sh>
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
use Webauthn\PublicKeyCredentialDescriptor;
use Webauthn\PublicKeyCredentialDescriptorCollection;
use Webauthn\SecurityBundle\Model\CanHaveRegisteredSecurityDevices;

/**
 * @ORM\Table(name="users")
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @UniqueEntity("username")
 */
class User implements UserInterface, CanHaveRegisteredSecurityDevices
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

    public function __construct(string $id, string $username, string $displayName, array $roles)
    {
        $this->id = $id;
        $this->username = $username;
        $this->roles = $roles;
        $this->credentials = new ArrayCollection();
        $this->displayName = $displayName;
        $this->created_at = new \DateTimeImmutable();
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getSecurityDeviceCredentialIds(): iterable
    {
        $collection = new PublicKeyCredentialDescriptorCollection();
        foreach ($this->credentials as $credential) {
            /** @var Credential $credential */
            $collection->add(new PublicKeyCredentialDescriptor(
                PublicKeyCredentialDescriptor::CREDENTIAL_TYPE_PUBLIC_KEY,
                $credential->getAttestedCredentialData()->getCredentialId(),
                []
            ));
        }

        yield from $collection;
    }

    public function addCredential(Credential $credential): void
    {
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

    public function getUserHandle(): string
    {
        return $this->id;
    }

    public function setPublicKeyCredentialDescriptorCollection(PublicKeyCredentialDescriptorCollection $credentials): void
    {
        $this->credentials = $credentials;
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

    public function getDisplayName(): ?string
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
