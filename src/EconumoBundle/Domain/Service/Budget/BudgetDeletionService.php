<?php

declare(strict_types=1);


namespace App\EconumoBundle\Domain\Service\Budget;


use App\EconumoBundle\Domain\Repository\UserRepositoryInterface;
use Throwable;
use App\EconumoBundle\Domain\Entity\ValueObject\Id;
use App\EconumoBundle\Domain\Repository\BudgetRepositoryInterface;
use App\EconumoBundle\Domain\Service\AntiCorruptionServiceInterface;

readonly class BudgetDeletionService
{
    public function __construct(
        private BudgetRepositoryInterface $budgetRepository,
        private AntiCorruptionServiceInterface $antiCorruptionService,
        private UserRepositoryInterface $userRepository
    ) {
    }

    public function deleteBudget(Id $budgetId): void
    {
        $budget = $this->budgetRepository->get($budgetId);
        $accessList = $budget->getAccessList();
        $budgetOwner = $budget->getUser();
        $this->antiCorruptionService->beginTransaction(__METHOD__);
        try {
            $affectedUsers = [];
            if ($budgetOwner->getDefaultBudgetId() && $budgetOwner->getDefaultBudgetId()->isEqual($budgetId)) {
                $budgetOwner->updateDefaultBudget(null);
                $affectedUsers[$budgetOwner->getId()->getValue()] = $budgetOwner;
            }

            foreach ($accessList as $accessItem) {
                $budgetUser = $accessItem->getUser();
                if (array_key_exists($budgetUser->getId()->getValue(), $affectedUsers)) {
                    continue;
                }

                if ($budgetUser->getDefaultBudgetId() && $budgetUser->getDefaultBudgetId()->isEqual($budgetId)) {
                    $budgetUser->updateDefaultBudget(null);
                    $affectedUsers[$budgetUser->getId()->getValue()] = $budgetUser;
                }
            }

            if ($affectedUsers !== []) {
                $this->userRepository->save($affectedUsers);
            }

            $this->budgetRepository->delete([$budget]);
            $this->antiCorruptionService->commit(__METHOD__);
        } catch (Throwable $throwable) {
            $this->antiCorruptionService->rollback(__METHOD__);
            throw $throwable;
        }
    }
}
