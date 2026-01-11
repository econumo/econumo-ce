<?php

declare(strict_types=1);


namespace App\EconumoBundle\Application\Budget\Assembler;

use App\EconumoBundle\Application\Budget\Dto\BudgetMetaResultDto;
use App\EconumoBundle\Application\Budget\Assembler\BudgetAccessToResultDtoAssembler;
use App\EconumoBundle\Domain\Service\Budget\Dto\BudgetMetaDto;

readonly class BudgetMetaToResultDtoAssembler
{
    public function __construct(
        private BudgetAccessToResultDtoAssembler $budgetAccessToResultDtoAssembler,
    ) {
    }

    public function assemble(BudgetMetaDto $dto): BudgetMetaResultDto
    {
        $result = new BudgetMetaResultDto();
        $result->id = $dto->id->getValue();
        $result->ownerUserId = $dto->ownerUserId->getValue();
        $result->name = $dto->budgetName->getValue();
        $result->startedAt = $dto->startedAt->format('Y-m-d H:i:s');
        $result->currencyId = $dto->currencyId->getValue();
        $result->access = [];
        foreach ($dto->access as $budgetAccess) {
            $result->access[] = $this->budgetAccessToResultDtoAssembler->assemble($budgetAccess);
        }

        return $result;
    }
}
