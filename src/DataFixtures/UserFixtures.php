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

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Webauthn\PublicKeyCredentialDescriptorCollection;

class UserFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        foreach ($this->getUsers() as $data) {
            $devices = new PublicKeyCredentialDescriptorCollection();
            foreach ($data['devices'] as $device) {
                $devices->add($device);
            }

            $user = new User(
                $data['id'],
                $data['username'],
                $data['display_name'],
                $data['roles'],
                $devices
            );
            $manager->persist($user);
        }

        $manager->flush();
    }

    private function getUsers(): array
    {
        return [];
    }
}
