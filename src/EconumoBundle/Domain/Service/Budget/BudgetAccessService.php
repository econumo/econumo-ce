<?php

declare(strict_types=1);


namespace App\EconumoBundle\Domain\Service\Budget;


use App\EconumoBundle\Domain\Entity\ValueObject\Id;
use App\EconumoBundle\Domain\Entity\ValueObject\UserRole;
use App\EconumoBundle\Domain\Exception\AccessDeniedException;
use App\EconumoBundle\Domain\Repository\BudgetRepositoryInterface;
use App\EconumoBundle\Domain\Service\Budget\BudgetAccessServiceInterface;

readonly class BudgetAccessService implements BudgetAccessServiceInterface
{
    public function __construct(
        private BudgetRepositoryInterface $budgetRepository,
    ) {
    }

    public function canReadBudget(Id $userId, Id $budgetId): bool
    {
        try {
            $this->getBudgetRole($userId, $budgetId);
        } catch (AccessDeniedException) {
            return false;
        }

        return true;
    }

    public function getBudgetRole(Id $userId, Id $budgetId): UserRole
    {
        $budget = $this->budgetRepository->get($budgetId);
        if ($budget->getUser()->getId()->isEqual($userId)) {
            return UserRole::owner();
        }

        $accessList = $budget->getAccessList();
        foreach ($accessList as $access) {
            if ($access->getUserId()->isEqual($userId)) {
                if (!$access->isAccepted()) {
                    throw new AccessDeniedException();
                }

                return $access->getRole();
            }
        }

        throw new AccessDeniedException();
    }

    public function canDeleteBudget(Id $userId, Id $budgetId): bool
    {
        try {
            $role = $this->getBudgetRole($userId, $budgetId);
            if ($role->isOwner() || $role->isAdmin()) {
                return true;
            }
        } catch (AccessDeniedException) {
            return false;
        }

        return false;
    }

    public function canUpdateBudget(Id $userId, Id $budgetId): bool
    {
        try {
            $role = $this->getBudgetRole($userId, $budgetId);
            if ($role->isOwner() || $role->isAdmin() || $role->isUser()) {
                return true;
            }
        } catch (AccessDeniedException) {
            return false;
        }

        return false;
    }

    public function canResetBudget(Id $userId, Id $budgetId): bool
    {
        try {
            $role = $this->getBudgetRole($userId, $budgetId);
            if ($role->isOwner() || $role->isAdmin()) {
                return true;
            }
        } catch (AccessDeniedException) {
            return false;
        }

        return false;
    }

    public function canEditBudget(Id $userId, Id $budgetId): bool
    {
        try {
            $role = $this->getBudgetRole($userId, $budgetId);
            if ($role->isOwner() || $role->isAdmin() || $role->isUser()) {
                return true;
            }
        } catch (AccessDeniedException) {
            return false;
        }

        return false;
    }

    public function canShareBudget(Id $userId, Id $budgetId): bool
    {
        try {
            $role = $this->getBudgetRole($userId, $budgetId);
            if ($role->isOwner() || $role->isAdmin()) {
                return true;
            }
        } catch (AccessDeniedException) {
            return false;
        }

        return true;
    }

    public function canAcceptBudget(Id $userId, Id $budgetId): bool
    {
        try {
            $budget = $this->budgetRepository->get($budgetId);
            $accessList = $budget->getAccessList();
            foreach ($accessList as $access) {
                if ($access->getUserId()->isEqual($userId) && !$access->isAccepted()) {
                    return true;
                }
            }
        } catch (AccessDeniedException) {
            return false;
        }

        return false;
    }

    public function canDeclineBudget(Id $userId, Id $budgetId): bool
    {
        try {
            $budget = $this->budgetRepository->get($budgetId);
            $accessList = $budget->getAccessList();
            foreach ($accessList as $access) {
                if ($access->getUserId()->isEqual($userId)) {
                    return true;
                }
            }
        } catch (AccessDeniedException) {
            return false;
        }

        return false;
    }
}
