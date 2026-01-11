<?php

declare(strict_types=1);

namespace App\EconumoBundle\Domain\Service\Currency;

use App\EconumoBundle\Domain\Service\Dto\CurrencyRateDto;

interface CurrencyRatesUpdateServiceInterface
{
    /**
     * @param CurrencyRateDto[] $currencyRates
     * @return int
     */
    public function updateCurrencyRates(array $currencyRates): int;
}
