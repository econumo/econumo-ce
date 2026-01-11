<?php

namespace App\EconumoBundle\DataFixtures;

use App\EconumoBundle\DataFixtures\AbstractFixture;
use App\EconumoBundle\DataFixtures\CurrenciesFixtures;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class CurrenciesRateFixtures extends AbstractFixture implements DependentFixtureInterface
{
    public string $tableName = 'currencies_rates';

    public function getDependencies(): array
    {
        return [CurrenciesFixtures::class];
    }
}
