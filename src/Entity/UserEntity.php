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

use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Webauthn\PublicKeyCredentialUserEntity;

/**
 * @ORM\Table(name="user_entities")
 * @ORM\Entity(repositoryClass="App\Repository\PublicKeyCredentialUserEntityRepository")
 */
class UserEntity extends PublicKeyCredentialUserEntity
{
    /**
     * @ORM\Id
     * @ORM\Column(name="string")
     * @ORM\GeneratedValue(strategy="NONE")
     */
    protected $id;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    protected $created_at;

    public function __construct(string $name, string $id, string $displayName, ?string $icon = null)
    {
        parent::__construct($name, $id, $displayName, $icon);
        $this->created_at = new DateTimeImmutable();
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->created_at;
    }
}
