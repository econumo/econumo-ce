<?php

declare(strict_types=1);

namespace App\EconumoBundle\Domain\Repository;

use App\EconumoBundle\Domain\Entity\BudgetFolder;
use App\EconumoBundle\Domain\Entity\ValueObject\Id;

interface BudgetFolderRepositoryInterface
{
    public function getNextIdentity(): Id;

    /**
     * @return BudgetFolder
     */
    public function get(Id $id): BudgetFolder;

    /**
     * @return BudgetFolder[]
     */
    public function getByBudgetId(Id $budgetId): array;

    /**
     * @param BudgetFolder[] $items
     * @return void
     */
    public function save(array $items): void;

    /**
     * @param BudgetFolder[] $items
     * @return void
     */
    public function delete(array $items): void;

    public function getReference(Id $id): BudgetFolder;

    public function deleteByBudgetId(Id $budgetId): void;
}