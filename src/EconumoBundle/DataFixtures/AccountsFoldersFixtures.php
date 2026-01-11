<?php

namespace App\EconumoBundle\DataFixtures;

use App\EconumoBundle\DataFixtures\AbstractFixture;
use App\EconumoBundle\DataFixtures\AccountsFixtures;
use App\EconumoBundle\DataFixtures\FoldersFixtures;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class AccountsFoldersFixtures extends AbstractFixture implements DependentFixtureInterface
{
    public string $tableName = 'accounts_folders';

    public function getDependencies(): array
    {
        return [FoldersFixtures::class, AccountsFixtures::class];
    }
}
