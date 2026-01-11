<?php

declare(strict_types=1);


namespace App\EconumoBundle\Domain\Service\Currency\Dto;


use App\EconumoBundle\Domain\Entity\Currency;
use DateTimeInterface;

readonly class AverageCurrencyRatesDto
{
    public function __construct(
        public Currency $baseCurrency,
        public DateTimeInterface $periodStart,
        public DateTimeInterface $periodEnd,
        /** @var array */
        public array $rates
    ) {
    }
}
