<?php

namespace App\EconumoBundle\DataFixtures;

use App\EconumoBundle\DataFixtures\AbstractFixture;
use App\EconumoBundle\DataFixtures\CurrenciesFixtures;
use App\EconumoBundle\DataFixtures\UsersFixtures;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class AccountsFixtures extends AbstractFixture implements DependentFixtureInterface
{
    public string $tableName = 'accounts';

    public function getDependencies(): array
    {
        return [UsersFixtures::class, CurrenciesFixtures::class];
    }
}
