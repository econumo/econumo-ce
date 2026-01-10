<?php

declare(strict_types=1);

namespace App\EconumoBundle\Domain\Service\Dto;

use App\EconumoBundle\Domain\Entity\ValueObject\CurrencyCode;
use App\EconumoBundle\Domain\Entity\ValueObject\Id;
use App\EconumoBundle\Domain\Entity\ValueObject\DecimalNumber;
use DateTimeInterface;

class FullCurrencyRateDto
{
    public Id $currencyId;

    public CurrencyCode $currencyCode;

    public Id $baseCurrencyId;

    public CurrencyCode $baseCurrencyCode;

    public DecimalNumber $rate;

    public DateTimeInterface $date;
}
