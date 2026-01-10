<?php

declare(strict_types=1);

namespace App\EconumoBundle\Domain\Repository;

use App\EconumoBundle\Domain\Entity\BudgetElementLimit;
use App\EconumoBundle\Domain\Entity\ValueObject\Id;
use App\EconumoBundle\Domain\Entity\ValueObject\DecimalNumber;
use DateTimeInterface;

interface BudgetElementLimitRepositoryInterface
{
    public function getNextIdentity(): Id;

    /**
     * @return BudgetElementLimit[]
     */
    public function getByBudgetIdAndPeriod(Id $budgetId, DateTimeInterface $period): array;

    /**
     * @param BudgetElementLimit[] $items
     * @return void
     */
    public function save(array $items): void;

    /**
     * @param BudgetElementLimit[] $items
     * @return void
     */
    public function delete(array $items): void;

    public function deleteByBudgetId(Id $budgetId): void;

    public function deleteByElementId(Id $elementId): void;

    /**
     * @param Id[] $elementIds
     * @return void
     */
    public function deleteByElementIds(array $elementIds): void;

    public function getSummarizedLimitsForPeriod(Id $budgetId, DateTimeInterface $periodStart, DateTimeInterface $periodEnd): array;

    /**
     * @param Id[] $externalIds
     * @return DecimalNumber[]
     */
    public function getSummarizedAmountsForElements(Id $budgetId, array $externalIds): array;

    /**
     * @return BudgetElementLimit[]
     */
    public function getByBudgetIdAndElementId(Id $budgetId, Id $externalId): array;

    /**
     * @return BudgetElementLimit|null
     */
    public function get(Id $elementId, DateTimeInterface $period): ?BudgetElementLimit;
}