<?php

declare(strict_types=1);

namespace App\EconumoBundle\Application\Budget\Assembler;

use App\EconumoBundle\Application\Budget\Dto\GrantAccessV1RequestDto;
use App\EconumoBundle\Application\Budget\Dto\GrantAccessV1ResultDto;
use App\EconumoBundle\Domain\Service\Budget\Dto\BudgetMetaDto;

readonly class GrantAccessV1ResultAssembler
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
    ): GrantAccessV1ResultDto {
        $result = new GrantAccessV1ResultDto();
        $result->items = [];
        foreach ($budgets as $budget) {
            $result->items[] = $this->budgetMetaToResultDtoAssembler->assemble($budget);
        }

        return $result;
    }
}
