<?php

declare(strict_types=1);

namespace App\EconumoBundle\Domain\Repository;

use App\EconumoBundle\Domain\Entity\CurrencyRate;
use App\EconumoBundle\Domain\Entity\ValueObject\Id;
use DateTimeInterface;

interface CurrencyRateRepositoryInterface
{
    public function getNextIdentity(): Id;

    public function get(Id $currencyId, Id $baseCurrencyId, DateTimeInterface $date): CurrencyRate;

    public function getLatestDate(Id $baseCurrencyId, ?DateTimeInterface $date = null): DateTimeInterface;

    /**
     * @return CurrencyRate[]
     */
    public function getAll(?DateTimeInterface $date = null): array;

    /**
     * @param CurrencyRate[] $items
     */
    public function save(array $items): void;

    /**
     * @return array
     */
    public function getAverage(DateTimeInterface $startDate, DateTimeInterface $endDate, Id $baseCurrencyId): array;
}
