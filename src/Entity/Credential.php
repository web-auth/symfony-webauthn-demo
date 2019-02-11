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

use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;
use Webauthn\AttestedCredentialData;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CredentialRepository")
 */
class Credential
{
    /**
     * @ORM\Id
     * @ORM\Column(type="string", length=255)
     */
    private $id;

    /**
     * @ORM\Column(type="blob", length=255)
     * @Assert\NotBlank
     */
    private $credential_id;

    /**
     * @ORM\Column(type="attested_credential_data")
     * @Assert\NotBlank
     */
    private $attested_credential_data;

    /**
     * @ORM\Column(type="integer")
     * @Assert\GreaterThanOrEqual(0)
     */
    private $counter;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="credentials",cascade={"persist"})
     */
    private $user;

    public function __construct(AttestedCredentialData $attested_credential_data, int $counter, User $user)
    {
        $this->id = Uuid::uuid4()->toString();
        $this->credential_id = $attested_credential_data->getCredentialId();
        $this->attested_credential_data = $attested_credential_data;
        $this->counter = $counter;
        $this->user = $user;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getAttestedCredentialData(): AttestedCredentialData
    {
        return $this->attested_credential_data;
    }

    public function getCounter(): int
    {
        return $this->counter;
    }

    public function setCounter(int $counter): void
    {
        $this->counter = $counter;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getUserHandle(): string
    {
        return $this->user->getId();
    }
}
