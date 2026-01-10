<?php
declare(strict_types=1);

namespace App\EconumoBundle\Domain\Repository;

use App\EconumoBundle\Domain\Entity\Budget;
use App\EconumoBundle\Domain\Entity\ValueObject\Id;

interface BudgetRepositoryInterface
{
    public function getNextIdentity(): Id;

    public function get(Id $id): Budget;

    /**
     * @return Budget[]
     */
    public function getByUserId(Id $userId): array;

    /**
     * @param Budget[] $items
     */
    public function save(array $items): void;

    public function getReference(Id $id): Budget;

    /**
     * @param Budget[] $items
     */
    public function delete(array $items): void;
}
