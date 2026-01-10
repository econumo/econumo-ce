<?php

declare(strict_types=1);


namespace App\EconumoBundle\Domain\Service\Currency\Dto;


use App\EconumoBundle\Domain\Entity\ValueObject\Id;
use App\EconumoBundle\Domain\Entity\ValueObject\DecimalNumber;
use DateTimeInterface;

readonly class CurrencyConvertorDto
{
    public function __construct(
        public DateTimeInterface $periodStart,
        public DateTimeInterface $periodEnd,
        public Id $fromCurrencyId,
        public Id $toCurrencyId,
        public DecimalNumber $amount
    ) {
    }
}
