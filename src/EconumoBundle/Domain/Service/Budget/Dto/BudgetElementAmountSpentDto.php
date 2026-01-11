<?php

declare(strict_types=1);

namespace App\EconumoBundle\Domain\Service\Budget\Dto;

use App\EconumoBundle\Domain\Entity\ValueObject\Id;
use App\EconumoBundle\Domain\Entity\ValueObject\DecimalNumber;
use DateTimeInterface;

readonly class BudgetElementAmountSpentDto
{
    public function __construct(
        public Id $currencyId,
        public DecimalNumber $amount,
        public DateTimeInterface $periodStart,
        public DateTimeInterface $periodEnd,
    ) {
    }
}