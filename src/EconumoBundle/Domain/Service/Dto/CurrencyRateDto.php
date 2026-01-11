<?php

declare(strict_types=1);

namespace App\EconumoBundle\Domain\Service\Dto;

use App\EconumoBundle\Domain\Entity\ValueObject\CurrencyCode;
use App\EconumoBundle\Domain\Entity\ValueObject\DecimalNumber;
use DateTimeInterface;

class CurrencyRateDto
{
    public CurrencyCode $code;

    public CurrencyCode $base;

    public DecimalNumber $rate;

    public DateTimeInterface $date;
}
