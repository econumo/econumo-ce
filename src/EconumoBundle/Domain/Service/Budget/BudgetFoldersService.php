<?php

declare(strict_types=1);


namespace App\EconumoBundle\Domain\Service\Budget;


use App\EconumoBundle\Domain\Entity\BudgetFolder;
use App\EconumoBundle\Domain\Entity\ValueObject\Id;
use App\EconumoBundle\Domain\Exception\BudgetFolderMismatchException;
use App\EconumoBundle\Domain\Repository\BudgetFolderRepositoryInterface;
use App\EconumoBundle\Domain\Service\Budget\Dto\BudgetStructureOrderItemDto;

readonly class BudgetFoldersService
{
    public function __construct(
        private BudgetFolderRepositoryInterface $budgetFolderRepository,
        private BudgetElementsActionsService $budgetElementsActionsService,
    ) {
    }

    /**
     * @param BudgetStructureOrderItemDto[] $affectedFolders
     */
    public function orderFolders(Id $budgetId, array $affectedFolders): void
    {
        $budgetFolders = $this->budgetFolderRepository->getByBudgetId($budgetId);
        $tmpAffectedFolders = [];
        foreach ($affectedFolders as $folder) {
            $tmpAffectedFolders[$folder->id->getValue()] = $folder;
        }

        foreach ($budgetFolders as $budgetFolder) {
            if (!array_key_exists($budgetFolder->getId()->getValue(), $tmpAffectedFolders)) {
                continue;
            }

            if (!$budgetFolder->getBudget()->getId()->isEqual($budgetId)) {
                throw new BudgetFolderMismatchException();
            }

            $budgetFolder->updatePosition($tmpAffectedFolders[$budgetFolder->getId()->getValue()]->position);
        }

        usort($budgetFolders, static fn(BudgetFolder $a, BudgetFolder $b): int => $a->getPosition() <=> $b->getPosition());

        $position = 0;
        foreach ($budgetFolders as $budgetFolder) {
            $budgetFolder->updatePosition($position++);
        }

        $this->budgetFolderRepository->save($budgetFolders);
        $this->budgetElementsActionsService->restoreElementsOrder($budgetId);
    }
}
