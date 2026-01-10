<?php

declare(strict_types=1);


namespace App\EconumoBundle\Domain\Service\Budget\Builder;


use App\EconumoBundle\Domain\Entity\Budget;
use App\EconumoBundle\Domain\Entity\ValueObject\DecimalNumber;
use App\EconumoBundle\Domain\Repository\BudgetElementLimitRepositoryInterface;
use App\EconumoBundle\Domain\Service\Budget\Dto\BudgetElementBudgetedAmountDto;
use App\EconumoBundle\Domain\Service\Budget\Dto\BudgetFiltersDto;
use DateTimeInterface;

readonly class BudgetElementsLimitsBuilder
{
    public function __construct(
        private BudgetElementLimitRepositoryInterface $budgetElementLimitRepository
    ) {
    }

    /**
     * @return BudgetElementBudgetedAmountDto[]
     */
    public function build(
        Budget $budget,
        BudgetFiltersDto $budgetFilters
    ): array {
        $data = [];
        $data = $this->getElementsLimits($budget, $budgetFilters->periodStart, $data);
        $data = $this->getElementsOverallLimits(
            $budget,
            $budget->getStartedAt(),
            $budgetFilters->periodStart,
            $data
        );

        $result = [];
        foreach ($data as $index => $item) {
            $item = new BudgetElementBudgetedAmountDto(
                $item['id'],
                $item['type'],
                $item['budgeted'],
                $item['budgetedBefore']
            );
            $result[$index] = $item;
        }

        return $result;
    }

    private function getKey(string $id, string $type): string
    {
        return sprintf('%s-%s', $id, $type);
    }

    private function getElementsLimits(
        Budget $budget,
        DateTimeInterface $periodStart,
        array $data
    ): array {
        $elementsLimits = $this->budgetElementLimitRepository->getByBudgetIdAndPeriod($budget->getId(), $periodStart);
        foreach ($elementsLimits as $elementLimit) {
            $index = $this->getKey(
                $elementLimit->getElement()->getExternalId()->getValue(),
                $elementLimit->getElement()->getType()->getAlias()
            );
            if (!array_key_exists($index, $data)) {
                $data[$index] = [
                    'budgetedBefore' => new DecimalNumber(),
                    'id' => $elementLimit->getElement()->getExternalId(),
                    'type' => $elementLimit->getElement()->getType(),
                ];
            }

            $data[$index]['budgeted'] = $elementLimit->getAmount();
        }

        return $data;
    }

    private function getElementsOverallLimits(
        Budget $budget,
        DateTimeInterface $periodStart,
        DateTimeInterface $periodEnd,
        array $data
    ): array {
        $summarizedLimits = $this->budgetElementLimitRepository->getSummarizedLimitsForPeriod(
            $budget->getId(),
            $periodStart,
            $periodEnd
        );
        foreach ($summarizedLimits as $summarizedLimit) {
            $index = $this->getKey(
                $summarizedLimit['elementId']->getValue(),
                $summarizedLimit['elementType']->getAlias()
            );
            if (!array_key_exists($index, $data)) {
                $data[$index] = [
                    'budgeted' => null,
                    'budgetedBefore' => new DecimalNumber(),
                    'id' => $summarizedLimit['elementId'],
                    'type' => $summarizedLimit['elementType'],
                ];
            }

            $data[$index]['budgetedBefore'] = $data[$index]['budgetedBefore']->add($summarizedLimit['amount']);
        }

        return $data;
    }
}
