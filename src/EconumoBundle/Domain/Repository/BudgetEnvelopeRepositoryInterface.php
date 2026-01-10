<?php

declare(strict_types=1);

namespace App\EconumoBundle\Domain\Repository;

use App\EconumoBundle\Domain\Entity\BudgetEnvelope;
use App\EconumoBundle\Domain\Entity\ValueObject\Id;
use App\EconumoBundle\Domain\Exception\NotFoundException;

interface BudgetEnvelopeRepositoryInterface
{
    public function getNextIdentity(): Id;

    /**
     * @return BudgetEnvelope[]
     */
    public function getByBudgetId(Id $budgetId, bool $onlyActive = null): array;

    /**
     * @throws NotFoundException
     */
    public function get(Id $id): BudgetEnvelope;

    /**
     * @param BudgetEnvelope[] $items
     * @return void
     */
    public function save(array $items): void;

    /**
     * @param BudgetEnvelope[] $items
     * @return void
     */
    public function delete(array $items): void;

    public function getReference(Id $id): BudgetEnvelope;

    /**
     * @param Id[] $categoriesIds
     * @return void
     */
    public function deleteAssociationsWithCategories(Id $budgetId, array $categoriesIds): void;
}