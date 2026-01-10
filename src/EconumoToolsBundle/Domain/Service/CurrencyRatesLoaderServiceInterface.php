<?php

declare(strict_types=1);

namespace App\EconumoToolsBundle\Domain\Service;

use App\EconumoBundle\Domain\Service\Dto\CurrencyRateDto;
use DateTimeInterface;

interface CurrencyRatesLoaderServiceInterface
{
    /**
     * @return CurrencyRateDto[]
     */
    public function loadCurrencyRates(DateTimeInterface $date): array;
}