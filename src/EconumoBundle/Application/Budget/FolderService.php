<?php

declare(strict_types=1);

namespace App\EconumoBundle\Application\Budget;

use App\EconumoBundle\Application\Budget\Dto\CreateBudgetFolderV1RequestDto;
use App\EconumoBundle\Application\Budget\Dto\CreateBudgetFolderV1ResultDto;
use App\EconumoBundle\Application\Budget\Assembler\CreateFolderV1ResultAssembler;
use App\EconumoBundle\Application\Exception\AccessDeniedException;
use App\EconumoBundle\Domain\Entity\ValueObject\BudgetFolderName;
use App\EconumoBundle\Domain\Entity\ValueObject\Id;
use App\EconumoBundle\Domain\Service\Budget\BudgetAccessServiceInterface;
use App\EconumoBundle\Domain\Service\Budget\BudgetFolderServiceInterface;
use App\EconumoBundle\Application\Budget\Dto\DeleteFolderV1RequestDto;
use App\EconumoBundle\Application\Budget\Dto\DeleteFolderV1ResultDto;
use App\EconumoBundle\Application\Budget\Assembler\DeleteFolderV1ResultAssembler;
use App\EconumoBundle\Application\Budget\Dto\UpdateBudgetFolderV1RequestDto;
use App\EconumoBundle\Application\Budget\Dto\UpdateBudgetFolderV1ResultDto;
use App\EconumoBundle\Application\Budget\Assembler\UpdateFolderV1ResultAssembler;

readonly class FolderService
{
    public function __construct(
        private CreateFolderV1ResultAssembler $createFolderV1ResultAssembler,
        private BudgetAccessServiceInterface $budgetAccessService,
        private BudgetFolderServiceInterface $budgetFolderService,
        private DeleteFolderV1ResultAssembler $deleteFolderV1ResultAssembler,
        private UpdateFolderV1ResultAssembler $updateFolderV1ResultAssembler,
    ) {
    }

    public function createFolder(
        CreateBudgetFolderV1RequestDto $dto,
        Id $userId
    ): CreateBudgetFolderV1ResultDto {
        $budgetId = new Id($dto->budgetId);
        if (!$this->budgetAccessService->canUpdateBudget($userId, $budgetId)) {
            throw new AccessDeniedException();
        }

        $folderId = new Id($dto->id);
        $folderName = new BudgetFolderName($dto->name);
        $folder = $this->budgetFolderService->create($budgetId, $folderId, $folderName);
        return $this->createFolderV1ResultAssembler->assemble($folder);
    }

    public function deleteFolder(
        DeleteFolderV1RequestDto $dto,
        Id $userId
    ): DeleteFolderV1ResultDto {
        $budgetId = new Id($dto->budgetId);
        $folderId = new Id($dto->id);
        if (!$this->budgetAccessService->canUpdateBudget($userId, $budgetId)) {
            throw new AccessDeniedException();
        }

        $this->budgetFolderService->delete($budgetId, $folderId);
        return $this->deleteFolderV1ResultAssembler->assemble($dto);
    }

    public function updateFolder(
        UpdateBudgetFolderV1RequestDto $dto,
        Id $userId
    ): UpdateBudgetFolderV1ResultDto {
        $budgetId = new Id($dto->budgetId);
        $folderId = new Id($dto->id);
        $folderName = new BudgetFolderName($dto->name);
        if (!$this->budgetAccessService->canUpdateBudget($userId, $budgetId)) {
            throw new AccessDeniedException();
        }

        $folder = $this->budgetFolderService->update($budgetId, $folderId, $folderName);
        return $this->updateFolderV1ResultAssembler->assemble($folder);
    }
}
