<?php

declare(strict_types=1);

namespace App\EconumoBundle\Application\Budget;

use App\EconumoBundle\Application\Budget\Dto\MoveElementListV1RequestDto;
use App\EconumoBundle\Application\Budget\Dto\MoveElementListV1ResultDto;
use App\EconumoBundle\Application\Budget\Assembler\MoveElementListV1ResultAssembler;
use App\EconumoBundle\Application\Exception\AccessDeniedException;
use App\EconumoBundle\Domain\Entity\ValueObject\BudgetElementType;
use App\EconumoBundle\Domain\Entity\ValueObject\Id;
use App\EconumoBundle\Domain\Service\Budget\BudgetAccessServiceInterface;
use App\EconumoBundle\Domain\Service\Budget\BudgetServiceInterface;
use App\EconumoBundle\Domain\Service\Budget\Dto\BudgetStructureMoveElementDto;

readonly class ElementListService
{
    public function __construct(
        private MoveElementListV1ResultAssembler $moveElementListV1ResultAssembler,
        private BudgetAccessServiceInterface $budgetAccessService,
        private BudgetServiceInterface $budgetService,
    ) {
    }

    public function moveElementList(
        MoveElementListV1RequestDto $dto,
        Id $userId
    ): MoveElementListV1ResultDto {
        $budgetId = new Id($dto->budgetId);
        if (!$this->budgetAccessService->canUpdateBudget($userId, $budgetId)) {
            throw new AccessDeniedException();
        }

        $affectedElements = [];
        foreach ($dto->items as $item) {
            $affectedElements[$item->id] = new BudgetStructureMoveElementDto(
                new Id($item->id),
                $item->position,
                ($item->folderId === null ? null : new Id($item->folderId)),
            );
        }

        $this->budgetService->moveElements($userId, $budgetId, $affectedElements);
        return $this->moveElementListV1ResultAssembler->assemble($dto);
    }
}
