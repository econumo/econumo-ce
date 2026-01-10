<?php

declare(strict_types=1);

namespace App\EconumoBundle\Application\Budget\Assembler;

use App\EconumoBundle\Application\Budget\Assembler\BudgetDtoToResultDtoAssembler;
use App\EconumoBundle\Application\Budget\Dto\GetBudgetV1ResultDto;
use App\EconumoBundle\Domain\Service\Budget\Dto\BudgetDto;

readonly class GetBudgetV1ResultAssembler
{
    public function __construct(
        private BudgetDtoToResultDtoAssembler $budgetDtoToResultDtoAssembler
    ) {
    }

    public function assemble(
        BudgetDto $budget
    ): GetBudgetV1ResultDto {
        $result = new GetBudgetV1ResultDto();
        $result->item = $this->budgetDtoToResultDtoAssembler->assemble($budget);

        return $result;
    }
}
