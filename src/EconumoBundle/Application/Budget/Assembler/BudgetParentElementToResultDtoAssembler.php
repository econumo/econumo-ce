<?php

declare(strict_types=1);


namespace App\EconumoBundle\Application\Budget\Assembler;

use App\EconumoBundle\Application\Budget\Dto\BudgetStructureParentElementResultDto;
use App\EconumoBundle\Domain\Service\Budget\Dto\BudgetStructureParentElementDto;

readonly class BudgetParentElementToResultDtoAssembler
{
    public function __construct(
        private BudgetChildElementToResultDtoAssembler $budgetChildElementToResultDtoAssembler,
    ) {
    }

    public function assemble(BudgetStructureParentElementDto $dto): BudgetStructureParentElementResultDto
    {
        $result = new BudgetStructureParentElementResultDto();
        $result->id = $dto->id->getValue();
        $result->type = $dto->type->getValue();
        $result->name = $dto->name->getValue();
        $result->icon = $dto->icon->getValue();
        $result->currencyId = $dto->currencyId->getValue();
        $result->isArchived = $dto->isArchived ? 1 : 0;
        $result->folderId = $dto->folderId?->getValue();
        $result->position = $dto->position;
        $result->budgeted = $dto->budgeted->getValue();
        $result->available = $dto->available->getValue();
        $result->spent = $dto->spent->getValue();
        $result->budgetSpent = $dto->spentInBudgetCurrency->getValue();
        $result->ownerUserId = $dto->ownerId?->getValue();

        $result->children = [];
        foreach ($dto->children as $child) {
            $result->children[] = $this->budgetChildElementToResultDtoAssembler->assemble($child);
        }

        return $result;
    }
}
