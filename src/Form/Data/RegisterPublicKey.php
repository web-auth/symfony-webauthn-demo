<?php

/*
 * This file is part of the appname project.
 *
 * (c) Romain Gautier <mail@romain.sh>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Form\Data;

use Symfony\Component\Validator\Constraints as Assert;

class RegisterPublicKey
{
    /**
     * @Assert\NotBlank
     */
    private $attestation;

    public function getAttestation(): ?string
    {
        return $this->attestation;
    }

    public function setAttestation(string $attestation): void
    {
        $this->attestation = $attestation;
    }
}
