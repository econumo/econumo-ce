<?php

declare(strict_types=1);


namespace App\EconumoBundle\Domain\Service\Budget\Builder;


use App\EconumoBundle\Domain\Entity\Budget;
use App\EconumoBundle\Domain\Entity\ValueObject\BudgetElementType;
use App\EconumoBundle\Domain\Entity\ValueObject\Id;
use App\EconumoBundle\Domain\Entity\ValueObject\DecimalNumber;
use App\EconumoBundle\Domain\Repository\TransactionRepositoryInterface;
use App\EconumoBundle\Domain\Service\Budget\Dto\BudgetElementAmountSpentDto;
use App\EconumoBundle\Domain\Service\Budget\Dto\BudgetElementSpendingCategoryDto;
use App\EconumoBundle\Domain\Service\Budget\Dto\BudgetElementSpendingDto;
use App\EconumoBundle\Domain\Service\Budget\Dto\BudgetFiltersDto;
use DateInterval;
use DatePeriod;
use DateTimeInterface;

readonly class BudgetElementsSpendingBuilder
{
    public function __construct(
        private TransactionRepositoryInterface $transactionRepository,
    ) {
    }

    /**
     * @return BudgetElementSpendingDto[]
     */
    public function build(
        Budget $budget,
        BudgetFiltersDto $budgetFilters
    ): array {
        $data = $this->countSpending(
            [],
            $budgetFilters,
            $budgetFilters->periodStart,
            $budgetFilters->periodEnd,
            true
        );

        $interval = new DateInterval('P1M');
        $period = new DatePeriod($budget->getStartedAt(), $interval, $budgetFilters->periodStart);
        foreach ($period as $startDate) {
            $endDate = clone $startDate;
            $endDate->modify('next month');
            $data = $this->countSpending(
                $data,
                $budgetFilters,
                $startDate,
                $endDate,
                false
            );
        }

        return $data;
    }

    private function getKey(string $id, string $type): string
    {
        return sprintf('%s-%s', $id, $type);
    }

    /**
     * @return BudgetElementSpendingDto[]
     */
    private function countSpending(
        array $data,
        BudgetFiltersDto $budgetFilters,
        DateTimeInterface $periodStart,
        DateTimeInterface $periodEnd,
        bool $isCurrentPeriod
    ): array {
        $categoriesIds = [];
        foreach ($budgetFilters->categories as $category) {
            if ($category->getType()->isIncome()) {
                continue;
            }

            $categoriesIds[] = $category->getId();
        }

        $spending = $this->transactionRepository->countSpending(
            $categoriesIds,
            $budgetFilters->includedAccountsIds,
            $periodStart,
            $periodEnd
        );
        foreach ($spending as $item) {
            if (!empty($item['tag_id'])) {
                $type = BudgetElementType::tag();
                $id = new Id($item['tag_id']);
            } else {
                $type = BudgetElementType::category();
                $id = new Id($item['category_id']);
            }

            $index = $this->getKey($id->getValue(), $type->getAlias());

            if (!array_key_exists($index, $data)) {
                $data[$index] = new BudgetElementSpendingDto($id, $type);
            }

            if (!array_key_exists($item['category_id'], $data[$index]->spendingInCategories)) {
                $data[$index]->spendingInCategories[$item['category_id']] = new BudgetElementSpendingCategoryDto(
                    new Id($item['category_id']),
                    (empty($item['tag_id']) ? null : new Id($item['tag_id']))
                );
            }

            if ($isCurrentPeriod) {
                $data[$index]->spendingInCategories[$item['category_id']]->currenciesSpent[] = new BudgetElementAmountSpentDto(
                    new Id($item['currency_id']),
                    new DecimalNumber($item['amount']),
                    $periodStart,
                    $periodEnd
                );
            } else {
                $data[$index]->spendingInCategories[$item['category_id']]->currenciesSpentBefore[] = new BudgetElementAmountSpentDto(
                    new Id($item['currency_id']),
                    new DecimalNumber($item['amount']),
                    $periodStart,
                    $periodEnd
                );
            }
        }

        return $data;
    }
}
