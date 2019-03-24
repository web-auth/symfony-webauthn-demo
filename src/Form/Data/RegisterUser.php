<?php

/*
 * This file is part of the Webauthn Demo project.
 *
 * (c) Florent Morselli
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Form\Data;

use App\Validator\Constraints\UniqueUsername;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class RegisterUser
{
    /**
     * @Assert\NotBlank
     * @UniqueUsername
     * @Assert\Length(max = 100)
     */
    private $username;

    /**
     * @Assert\NotBlank
     * @Assert\Length(max = 100)
     */
    private $displayName;

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

    /**
     * @param mixed $displayName
     */
    public function setDisplayName(string $displayName): void
    {
        $this->displayName = $displayName;
    }

    /**
     * @Assert\Callback
     *
     * @param mixed $payload
     */
    public function validate(ExecutionContextInterface $context, $payload)
    {
        if (filter_var($this->username, FILTER_VALIDATE_EMAIL)) {
            $context->buildViolation('The username should not be an e-amil address')
                ->atPath('username')
                ->addViolation();
        }
    }
}
