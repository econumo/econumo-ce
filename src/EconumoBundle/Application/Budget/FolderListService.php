<?php

declare(strict_types=1);

namespace App\EconumoBundle\Application\Budget;

use App\EconumoBundle\Application\Budget\Dto\OrderBudgetFolderListV1RequestDto;
use App\EconumoBundle\Application\Budget\Dto\OrderBudgetFolderListV1ResultDto;
use App\EconumoBundle\Application\Budget\Assembler\OrderFolderListV1ResultAssembler;
use App\EconumoBundle\Application\Exception\AccessDeniedException;
use App\EconumoBundle\Domain\Entity\ValueObject\BudgetElementType;
use App\EconumoBundle\Domain\Entity\ValueObject\Id;
use App\EconumoBundle\Domain\Service\Budget\BudgetAccessServiceInterface;
use App\EconumoBundle\Domain\Service\Budget\BudgetServiceInterface;
use App\EconumoBundle\Domain\Service\Budget\Dto\BudgetStructureMoveElementDto;
use App\EconumoBundle\Domain\Service\Budget\Dto\BudgetStructureOrderItemDto;

readonly class FolderListService
{
    public function __construct(
        private OrderFolderListV1ResultAssembler $orderFolderListV1ResultAssembler,
        private BudgetAccessServiceInterface $budgetAccessService,
        private BudgetServiceInterface $budgetService,
    ) {
    }

    public function orderFolderList(
        OrderBudgetFolderListV1RequestDto $dto,
        Id $userId
    ): OrderBudgetFolderListV1ResultDto {
        $budgetId = new Id($dto->budgetId);
        if (!$this->budgetAccessService->canUpdateBudget($userId, $budgetId)) {
            throw new AccessDeniedException();
        }

        $affectedFolders = [];
        foreach ($dto->items as $item) {
            $affectedFolders[$item->id] = new BudgetStructureOrderItemDto(
                new Id($item->id),
                $item->position,
            );
        }

        $this->budgetService->orderFolders($userId, $budgetId, $affectedFolders);
        return $this->orderFolderListV1ResultAssembler->assemble($dto);
    }
}
