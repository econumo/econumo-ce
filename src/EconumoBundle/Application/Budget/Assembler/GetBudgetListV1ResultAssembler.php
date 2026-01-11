<?php

declare(strict_types=1);

namespace App\EconumoBundle\Application\Budget\Assembler;

use App\EconumoBundle\Application\Budget\Dto\GetBudgetListV1ResultDto;
use App\EconumoBundle\Application\Budget\Assembler\BudgetMetaToResultDtoAssembler;
use App\EconumoBundle\Domain\Service\Budget\Dto\BudgetMetaDto;

readonly class GetBudgetListV1ResultAssembler
{
    public function __construct(
        private BudgetMetaToResultDtoAssembler $budgetMetaToResultDtoAssembler
    ) {
    }

    /**
     * @param BudgetMetaDto[] $budgets
     */
    public function assemble(
        array $budgets
    ): GetBudgetListV1ResultDto {
        $result = new GetBudgetListV1ResultDto();
        $result->items = [];
        foreach ($budgets as $budget) {
            $result->items[] = $this->budgetMetaToResultDtoAssembler->assemble($budget);
        }

        return $result;
    }
}
