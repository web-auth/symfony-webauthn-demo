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

use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Webauthn\AttestedCredentialData;
use Webauthn\PublicKeyCredentialDescriptor;
use Webauthn\PublicKeyCredentialSource as BasePublicKeyCredentialSource;
use Webauthn\TrustPath\TrustPath;

/**
 * @ORM\Table(name="public_key_credential_sources")
 * @ORM\Entity(repositoryClass="App\Repository\PublicKeyCredentialSourceRepository")
 */
class PublicKeyCredentialSource extends BasePublicKeyCredentialSource
{
    /**
     * @var string
     * @ORM\Id
     * @ORM\Column(type="string", length=100)
     */
    private $id;

    /**
     * @var \DateTimeImmutable
     * @ORM\Column(type="datetime_immutable")
     */
    private $createdAt;

    /**
     * @var string|null
     * @ORM\Column(type="string", nullable=true)
     */
    private $name;

    public function __construct(string $publicKeyCredentialId, string $type, array $transports, string $attestationType, TrustPath $trustPath, string $aaguid, string $credentialPublicKey, string $userHandle, int $counter)
    {
        $this->id = Uuid::uuid4()->toString();
        $this->createdAt = new \DateTimeImmutable();
        parent::__construct($publicKeyCredentialId, $type, $transports, $attestationType, $trustPath, $aaguid, $credentialPublicKey, $userHandle, $counter);
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    public function getPublicKeyCredentialDescriptor(): PublicKeyCredentialDescriptor
    {
        return new PublicKeyCredentialDescriptor(
            $this->getType(),
            $this->getPublicKeyCredentialId(),
            $this->getTransports()
        );
    }

    public function getAttestedCredentialData(): AttestedCredentialData
    {
        return new AttestedCredentialData(
            $this->getAaguid(),
            $this->getPublicKeyCredentialId(),
            $this->getCredentialPublicKey()
        );
    }
}
