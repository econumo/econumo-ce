<?php

declare(strict_types=1);

namespace App\EconumoBundle\Domain\Repository;

use App\EconumoBundle\Domain\Entity\BudgetElement;
use App\EconumoBundle\Domain\Entity\ValueObject\Id;

interface BudgetElementRepositoryInterface
{
    public function getNextIdentity(): Id;

    /**
     * @return BudgetElement[]
     */
    public function getByBudgetId(Id $budgetId): array;

    public function get(Id $budgetId, Id $externalElementId): BudgetElement;

    /**
     * @param BudgetElement[] $items
     * @return void
     */
    public function save(array $items): void;

    /**
     * @param BudgetElement[] $items
     * @return void
     */
    public function delete(array $items): void;

    public function getReference(Id $id): BudgetElement;

    public function getNextPosition(Id $budgetId, ?Id $folderId): int;

    /**
     * @return BudgetElement[]
     */
    public function getElementsByExternalId(Id $externalElementId): array;

    public function deleteByBudgetId(Id $budgetId): void;
}