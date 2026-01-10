<?php

declare(strict_types=1);


namespace App\EconumoBundle\Domain\Service\Budget\Dto;

readonly class BudgetFinancialSummaryDto
{
    public function __construct(
        /** @var CurrencyBalanceDto[] */
        public array $currencyBalances,
        /** @var AverageCurrencyRateDto[] */
        public array $averageCurrencyRates
    ) {
    }
}
