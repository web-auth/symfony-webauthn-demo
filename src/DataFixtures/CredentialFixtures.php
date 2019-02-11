<?php

/*
 * This file is part of the appname project.
 *
 * (c) Romain Gautier <mail@romain.sh>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\DataFixtures;

use App\Entity\Credential;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class CredentialFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        foreach ($this->getCredentials() as $data) {
            $credential = new Credential(
                $data['attested'],
                $data['counter']
            );
            $manager->persist($credential);
        }

        $manager->flush();
    }

    private function getCredentials(): array
    {
        return [];
    }
}
