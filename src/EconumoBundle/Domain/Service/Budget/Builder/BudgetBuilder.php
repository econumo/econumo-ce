<?php

declare(strict_types=1);

namespace App\EconumoBundle\Domain\Service\Budget\Builder;

use App\EconumoBundle\Domain\Entity\Budget;
use App\EconumoBundle\Domain\Entity\ValueObject\Id;
use App\EconumoBundle\Domain\Service\Budget\Dto\BudgetDto;
use DateTimeImmutable;
use DateTimeInterface;

readonly class BudgetBuilder
{
    public function __construct(
        private BudgetMetaBuilder $budgetMetaBuilder,
        private BudgetFiltersBuilder $budgetFiltersBuilder,
        private BudgetFinancialSummaryBuilder $budgetFinancialSummaryBuilder,
        private BudgetElementsSpendingBuilder $budgetElementsSpendingBuilder,
        private BudgetStructureBuilder $budgetStructureBuilder,
        private BudgetElementsLimitsBuilder $budgetElementsLimitsBuilder
    ) {
    }

    public function build(
        Id $userId,
        Budget $budget,
        DateTimeInterface $periodStart
    ): BudgetDto {
        $periodStart = DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $periodStart->format('Y-m-01 00:00:00'));
        $periodEnd = $periodStart->modify('next month');
        $budgetMeta = $this->budgetMetaBuilder->build($budget);
        $budgetFilters = $this->budgetFiltersBuilder->build($budget, $userId, $periodStart, $periodEnd);
        $budgetFinancialSummary = $this->budgetFinancialSummaryBuilder->build(
            $budget->getCurrencyId(),
            $budgetFilters->periodStart,
            $budgetFilters->periodEnd,
            $budgetFilters->currenciesIds,
            $budgetFilters->includedAccountsIds
        );
        $elementsLimits = $this->budgetElementsLimitsBuilder->build($budget, $budgetFilters);
        $elementsSpending = $this->budgetElementsSpendingBuilder->build($budget, $budgetFilters);
        $budgetStructure = $this->budgetStructureBuilder->build($budget, $budgetFilters, $elementsLimits, $elementsSpending);

        return new BudgetDto(
            $budgetMeta,
            $budgetFilters,
            $budgetFinancialSummary,
            $budgetStructure
        );
    }
}
