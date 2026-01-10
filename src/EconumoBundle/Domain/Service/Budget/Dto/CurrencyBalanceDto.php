<?php

declare(strict_types=1);

namespace App\EconumoBundle\Domain\Service\Budget\Dto;

use App\EconumoBundle\Domain\Entity\ValueObject\Id;
use App\EconumoBundle\Domain\Entity\ValueObject\DecimalNumber;

readonly class CurrencyBalanceDto
{
    public function __construct(
        public Id $currencyId,
        public ?DecimalNumber $startBalance,
        public ?DecimalNumber $endBalance,
        public ?DecimalNumber $income,
        public ?DecimalNumber $expenses,
        public ?DecimalNumber $exchanges,
        public ?DecimalNumber $holdings,
    ) {
    }
}