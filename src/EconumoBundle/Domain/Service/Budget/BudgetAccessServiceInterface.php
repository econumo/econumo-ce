<?php

declare(strict_types=1);

namespace App\EconumoBundle\Domain\Service\Budget;

use App\EconumoBundle\Domain\Entity\ValueObject\Id;
use App\EconumoBundle\Domain\Entity\ValueObject\UserRole;

interface BudgetAccessServiceInterface
{
    public function canReadBudget(Id $userId, Id $budgetId): bool;

    public function canDeleteBudget(Id $userId, Id $budgetId): bool;

    public function canEditBudget(Id $userId, Id $budgetId): bool;

    public function canShareBudget(Id $userId, Id $budgetId): bool;

    public function canAcceptBudget(Id $userId, Id $budgetId): bool;

    public function canDeclineBudget(Id $userId, Id $budgetId): bool;

    public function canUpdateBudget(Id $userId, Id $budgetId): bool;

    public function canResetBudget(Id $userId, Id $budgetId): bool;

    public function getBudgetRole(Id $userId, Id $budgetId): UserRole;
}
