<?php

declare(strict_types=1);

namespace App\EconumoBundle\Domain\Service\Budget;

use App\EconumoBundle\Domain\Entity\ValueObject\BudgetFolderName;
use App\EconumoBundle\Domain\Entity\ValueObject\Id;
use App\EconumoBundle\Domain\Exception\AccessDeniedException;
use App\EconumoBundle\Domain\Factory\BudgetFolderFactoryInterface;
use App\EconumoBundle\Domain\Repository\BudgetFolderRepositoryInterface;
use App\EconumoBundle\Domain\Repository\BudgetRepositoryInterface;
use App\EconumoBundle\Domain\Service\Budget\Assembler\BudgetStructureFolderDtoAssembler;
use App\EconumoBundle\Domain\Service\Budget\Dto\BudgetStructureFolderDto;

readonly class BudgetFolderService implements BudgetFolderServiceInterface
{
    public function __construct(
        private BudgetFolderFactoryInterface $budgetFolderFactory,
        private BudgetFolderRepositoryInterface $budgetFolderRepository,
        private BudgetStructureFolderDtoAssembler $budgetStructureFolderDtoAssembler,
        private BudgetRepositoryInterface $budgetRepository,
    ) {
    }

    public function create(Id $budgetId, Id $folderId, BudgetFolderName $name): BudgetStructureFolderDto
    {
        $toSave = [];
        $budget = $this->budgetRepository->find($budgetId);
        $newFolder = $this->budgetFolderFactory->create($budget->getId(), $folderId, $name);
        $toSave[] = $newFolder;

        $folders = $this->budgetFolderRepository->getByBudgetId($budgetId);
        $position = 0;
        foreach ($folders as $folder) {
            ++$position;
            if ($folder->getPosition() === $position) {
                continue;
            }

            $folder->updatePosition($position);
            $toSave[] = $folder;
        }

        $this->budgetFolderRepository->save($toSave);

        return $this->budgetStructureFolderDtoAssembler->assemble($newFolder);
    }

    public function delete(Id $budgetId, Id $folderId): void
    {
        $folder = $this->budgetFolderRepository->get($folderId);
        if (!$folder->getBudget()->getId()->isEqual($budgetId)) {
            throw new AccessDeniedException();
        }

        $this->budgetFolderRepository->delete([$folder]);

        $toSave = [];
        $folders = $this->budgetFolderRepository->getByBudgetId($folder->getBudget()->getId());
        $position = 0;
        foreach ($folders as $folder) {
            if ($folder->getPosition() === $position) {
                ++$position;
                continue;
            }

            $folder->updatePosition($position);
            $toSave[] = $folder;
            ++$position;
        }

        $this->budgetFolderRepository->save($toSave);
    }

    public function update(Id $budgetId, Id $folderId, BudgetFolderName $name): BudgetStructureFolderDto
    {
        $folder = $this->budgetFolderRepository->get($folderId);
        if (!$folder->getBudget()->getId()->isEqual($budgetId)) {
            throw new AccessDeniedException();
        }

        $folder->updateName($name);
        $this->budgetFolderRepository->save([$folder]);

        return $this->budgetStructureFolderDtoAssembler->assemble($folder);
    }
}
