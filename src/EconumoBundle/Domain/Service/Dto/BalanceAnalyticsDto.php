<?php

declare(strict_types=1);


namespace App\EconumoBundle\Domain\Service\Dto;

use App\EconumoBundle\Domain\Entity\ValueObject\DecimalNumber;
use DateTimeInterface;

class BalanceAnalyticsDto
{
    public DateTimeInterface $date;

    public DecimalNumber $balance;
}
