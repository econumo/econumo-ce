<?php

namespace App\EconumoBundle\DataFixtures;

use App\EconumoBundle\DataFixtures\AbstractFixture;
use App\EconumoBundle\DataFixtures\AccountsFixtures;
use App\EconumoBundle\DataFixtures\CategoriesFixtures;
use App\EconumoBundle\DataFixtures\PayeesFixtures;
use App\EconumoBundle\DataFixtures\TagsFixtures;
use App\EconumoBundle\DataFixtures\UsersFixtures;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class TransactionsFixtures extends AbstractFixture implements DependentFixtureInterface
{
    public string $tableName = 'transactions';

    public function getDependencies(): array
    {
        return [
            UsersFixtures::class,
            AccountsFixtures::class,
            CategoriesFixtures::class,
            TagsFixtures::class,
            PayeesFixtures::class
        ];
    }
}
