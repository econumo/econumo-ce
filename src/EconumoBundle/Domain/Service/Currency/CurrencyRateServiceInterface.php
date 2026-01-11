<?php

declare(strict_types=1);


namespace App\EconumoBundle\Domain\Service\Currency;

use App\EconumoBundle\Domain\Entity\CurrencyRate;
use App\EconumoBundle\Domain\Service\Currency\Dto\AverageCurrencyRatesDto;
use App\EconumoBundle\Domain\Service\Dto\FullCurrencyRateDto;
use DateTimeInterface;

interface CurrencyRateServiceInterface
{
    /**
     * @param DateTimeInterface $dateTime
     * @return CurrencyRate[]
     */
    public function getCurrencyRates(DateTimeInterface $dateTime): array;

    /**
     * @return CurrencyRate[]
     */
    public function getLatestCurrencyRates(): array;

    /**
     * @param DateTimeInterface $startDate
     * @param DateTimeInterface $endDate
     */
    public function getAverageCurrencyRates(DateTimeInterface $startDate, DateTimeInterface $endDate): AverageCurrencyRatesDto;

    /**
     * @param DateTimeInterface $startDate
     * @param DateTimeInterface $endDate
     * @return FullCurrencyRateDto[]
     */
    public function getAverageFullCurrencyRatesDtos(DateTimeInterface $startDate, DateTimeInterface $endDate): array;
}
