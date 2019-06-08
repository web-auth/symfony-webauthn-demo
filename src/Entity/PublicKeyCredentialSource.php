<?php

declare(strict_types=1);

/*
 * The MIT License (MIT)
 *
 * Copyright (c) 2014-2019 Spomky-Labs
 *
 * This software may be modified and distributed under the terms
 * of the MIT license.  See the LICENSE file for details.
 */

namespace App\Entity;

use Assert\Assertion;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use Webauthn\AttestedCredentialData;
use Webauthn\AuthenticatorAttestationResponse;
use Webauthn\PublicKeyCredential;
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
     * @ORM\GeneratedValue(strategy="NONE")
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

    public function __construct(string $publicKeyCredentialId, string $type, array $transports, string $attestationType, TrustPath $trustPath, UuidInterface $aaguid, string $credentialPublicKey, string $userHandle, int $counter)
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

    public static function createFromPublicKeyCredential(PublicKeyCredential $publicKeyCredential, string $userHandle): BasePublicKeyCredentialSource
    {
        $response = $publicKeyCredential->getResponse();
        Assertion::isInstanceOf($response, AuthenticatorAttestationResponse::class, 'This method is only available with public key credential containing an authenticator attestation response.');
        $publicKeyCredentialDescriptor = $publicKeyCredential->getPublicKeyCredentialDescriptor();
        $attestationStatement = $response->getAttestationObject()->getAttStmt();
        $authenticatorData = $response->getAttestationObject()->getAuthData();
        $attestedCredentialData = $authenticatorData->getAttestedCredentialData();
        Assertion::notNull($attestedCredentialData, 'No attested credential data available');

        return new self(
            $publicKeyCredentialDescriptor->getId(),
            $publicKeyCredentialDescriptor->getType(),
            $publicKeyCredentialDescriptor->getTransports(),
            $attestationStatement->getType(),
            $attestationStatement->getTrustPath(),
            $attestedCredentialData->getAaguid(),
            $attestedCredentialData->getCredentialPublicKey(),
            $userHandle,
            $authenticatorData->getSignCount()
        );
    }
}
