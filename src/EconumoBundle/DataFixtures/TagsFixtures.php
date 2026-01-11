<?php

namespace App\EconumoBundle\DataFixtures;

use App\EconumoBundle\DataFixtures\AbstractFixture;
use App\EconumoBundle\DataFixtures\UsersFixtures;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class TagsFixtures extends AbstractFixture implements DependentFixtureInterface
{
    public string $tableName = 'tags';

    public function getDependencies(): array
    {
        return [UsersFixtures::class];
    }
}
