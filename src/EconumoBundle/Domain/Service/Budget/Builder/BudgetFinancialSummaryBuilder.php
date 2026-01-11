<?php

declare(strict_types=1);

namespace App\EconumoBundle\Domain\Service\Budget\Builder;

use App\EconumoBundle\Domain\Entity\ValueObject\Id;
use App\EconumoBundle\Domain\Service\Budget\Assembler\AverageCurrencyRateDtoAssembler;
use App\EconumoBundle\Domain\Service\Budget\Assembler\CurrencyBalanceDtoAssembler;
use App\EconumoBundle\Domain\Service\Budget\Dto\AverageCurrencyRateDto;
use App\EconumoBundle\Domain\Service\Budget\Dto\BudgetFinancialSummaryDto;
use App\EconumoBundle\Domain\Service\Budget\Dto\CurrencyBalanceDto;
use DateTimeInterface;

readonly class BudgetFinancialSummaryBuilder
{
    public function __construct(
        private CurrencyBalanceDtoAssembler $currencyBalanceDtoAssembler,
        private AverageCurrencyRateDtoAssembler $averageCurrencyRateDtoAssembler,
    ) {
    }

    /**
     * @param Id[] $currenciesIds
     * @param Id[] $accountsIds
     */
    public function build(
        Id $budgetCurrencyId,
        DateTimeInterface $periodStart,
        DateTimeInterface $periodEnd,
        array $currenciesIds,
        array $accountsIds,
    ): BudgetFinancialSummaryDto {
        $currencyBalancesTmp = $this->getCurrencyBalances(
            $periodStart,
            $periodEnd,
            $accountsIds,
            $currenciesIds
        );
        $currencyBalances = [];
        foreach ($currencyBalancesTmp as $item) {
            if ($item->currencyId->isEqual($budgetCurrencyId)) {
                $currencyBalances[] = $item;
                break;
            }
        }

        foreach ($currencyBalancesTmp as $item) {
            if (!$item->currencyId->isEqual($budgetCurrencyId)) {
                $currencyBalances[] = $item;
            }
        }

        $averageCurrencyRates = $this->getAverageCurrencyRates(
            $periodStart,
            $periodEnd,
            []
        );

        return new BudgetFinancialSummaryDto(
            $currencyBalances,
            $averageCurrencyRates
        );
    }

    /**
     * @param Id[] $includedAccountsIds
     * @param Id[] $currenciesIds
     * @return CurrencyBalanceDto[]
     */
    private function getCurrencyBalances(
        DateTimeInterface $periodStart,
        DateTimeInterface $periodEnd,
        array $includedAccountsIds,
        array $currenciesIds
    ): array {
        return $this->currencyBalanceDtoAssembler->assemble(
            $periodStart,
            $periodEnd,
            $includedAccountsIds,
            $currenciesIds
        );
    }

    /**
     * @param Id[] $currenciesIds
     * @return AverageCurrencyRateDto[]
     */
    private function getAverageCurrencyRates(
        DateTimeInterface $periodStart,
        DateTimeInterface $periodEnd,
        array $currenciesIds
    ): array {
        return $this->averageCurrencyRateDtoAssembler->assemble($periodStart, $periodEnd, $currenciesIds);
    }
}