<?php

declare(strict_types=1);

namespace App\EconumoBundle\Application\Budget\Assembler;

use App\EconumoBundle\Application\Budget\Dto\UpdateBudgetV1ResultDto;
use App\EconumoBundle\Domain\Service\Budget\Dto\BudgetMetaDto;

readonly class UpdateBudgetV1ResultAssembler
{
    public function __construct(
        private BudgetMetaToResultDtoAssembler $budgetMetaToResultDtoAssembler
    ) {
    }

    public function assemble(
        BudgetMetaDto $budgetDto
    ): UpdateBudgetV1ResultDto {
        $result = new UpdateBudgetV1ResultDto();
        $result->item = $this->budgetMetaToResultDtoAssembler->assemble($budgetDto);

        return $result;
    }
}
