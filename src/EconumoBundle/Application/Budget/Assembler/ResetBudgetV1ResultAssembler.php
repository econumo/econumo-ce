<?php

declare(strict_types=1);

namespace App\EconumoBundle\Application\Budget\Assembler;

use App\EconumoBundle\Application\Budget\Dto\ResetBudgetV1ResultDto;
use App\EconumoBundle\Application\Budget\Assembler\BudgetMetaToResultDtoAssembler;
use App\EconumoBundle\Domain\Service\Budget\Dto\BudgetMetaDto;

readonly class ResetBudgetV1ResultAssembler
{
    public function __construct(
        private BudgetMetaToResultDtoAssembler $budgetMetaToResultDtoAssembler
    ) {
    }

    public function assemble(
        BudgetMetaDto $budgetDto
    ): ResetBudgetV1ResultDto {
        $result = new ResetBudgetV1ResultDto();
        $result->item = $this->budgetMetaToResultDtoAssembler->assemble($budgetDto);

        return $result;
    }
}
