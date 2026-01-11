<?php

namespace App\EconumoBundle\DataFixtures;


use App\EconumoBundle\DataFixtures\AbstractFixture;
use App\EconumoBundle\DataFixtures\UsersFixtures;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class UsersPasswordRequestFixtures extends AbstractFixture implements DependentFixtureInterface
{
    public string $tableName = 'users_password_requests';

    public function getDependencies(): array
    {
        return [UsersFixtures::class];
    }
}
