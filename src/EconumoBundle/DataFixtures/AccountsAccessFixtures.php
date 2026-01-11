<?php

namespace App\EconumoBundle\DataFixtures;

use App\EconumoBundle\DataFixtures\AbstractFixture;
use App\EconumoBundle\DataFixtures\AccountsFixtures;
use App\EconumoBundle\DataFixtures\UsersFixtures;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class AccountsAccessFixtures extends AbstractFixture implements DependentFixtureInterface
{
    public string $tableName = 'accounts_access';

    public function getDependencies(): array
    {
        return [UsersFixtures::class, AccountsFixtures::class];
    }
}
