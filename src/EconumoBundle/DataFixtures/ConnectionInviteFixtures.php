<?php

namespace App\EconumoBundle\DataFixtures;


use App\EconumoBundle\DataFixtures\AbstractFixture;
use App\EconumoBundle\DataFixtures\UsersFixtures;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class ConnectionInviteFixtures extends AbstractFixture implements DependentFixtureInterface
{
    public string $tableName = 'users_connections_invites';

    public function getDependencies(): array
    {
        return [UsersFixtures::class];
    }
}
