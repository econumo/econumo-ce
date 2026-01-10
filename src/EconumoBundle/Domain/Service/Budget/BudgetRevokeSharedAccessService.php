<?php

declare(strict_types=1);


namespace App\EconumoBundle\Domain\Service\Budget;


use Throwable;
use App\EconumoBundle\Domain\Entity\BudgetAccess;
use App\EconumoBundle\Domain\Entity\ValueObject\Id;
use App\EconumoBundle\Domain\Repository\BudgetAccessRepositoryInterface;
use App\EconumoBundle\Domain\Repository\BudgetRepositoryInterface;
use App\EconumoBundle\Domain\Service\AntiCorruptionServiceInterface;
use App\EconumoBundle\Domain\Service\UserServiceInterface;

readonly class BudgetRevokeSharedAccessService
{
    public function __construct(
        private BudgetAccessRepositoryInterface $budgetAccessRepository,
        private BudgetRepositoryInterface $budgetRepository,
        private BudgetElementServiceInterface $budgetElementService,
        private UserServiceInterface $userService,
        private AntiCorruptionServiceInterface $antiCorruptionService,
    ) {
    }

    public function revokeAccess(Id $budgetId, Id $userId): void
    {
        $access = null;
        $budgetInvites = $this->budgetAccessRepository->getByBudgetId($budgetId);
        foreach ($budgetInvites as $budgetInvite) {
            if ($budgetInvite->getUserId()->isEqual($userId)) {
                $access = $budgetInvite;
                break;
            }
        }

        if (!$access instanceof BudgetAccess) {
            return;
        }

        $this->removeUserAccess($access);
    }

    /**
     * @throws Throwable
     */
    private function removeUserAccess(BudgetAccess $access): void
    {
        $this->antiCorruptionService->beginTransaction(__METHOD__);
        try {
            $user = $access->getUser();
            if ($user->getDefaultBudgetId() && $user->getDefaultBudgetId()->isEqual($access->getBudgetId())) {
                $availableBudgets = $this->budgetRepository->getByUserId($access->getUserId());
                $newBudgetId = null;
                foreach ($availableBudgets as $budget) {
                    if (!$budget->getId()->isEqual($access->getBudgetId()) && $budget->isUserAccepted(
                            $access->getUserId()
                        )) {
                        $newBudgetId = $budget->getId();
                        break;
                    }
                }

                $this->userService->updateBudget($user->getId(), $newBudgetId);
            }

            $this->budgetAccessRepository->delete([$access]);
            $this->budgetElementService->deleteCategoriesElements($user->getId(), $access->getBudgetId());
            $this->budgetElementService->deleteTagsElements($user->getId(), $access->getBudgetId());
            $this->antiCorruptionService->commit(__METHOD__);
        } catch (Throwable $throwable) {
            $this->antiCorruptionService->rollback(__METHOD__);
            throw $throwable;
        }
    }
}
