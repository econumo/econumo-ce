<?php

declare(strict_types=1);

namespace App\EconumoBundle\Application\Budget\Assembler;

use App\EconumoBundle\Application\Budget\Dto\AcceptAccessV1ResultDto;
use App\EconumoBundle\Domain\Service\Budget\Dto\BudgetMetaDto;

readonly class AcceptAccessV1ResultAssembler
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
    ): AcceptAccessV1ResultDto {
        $result = new AcceptAccessV1ResultDto();
        $result->items = [];
        foreach ($budgets as $budget) {
            $result->items[] = $this->budgetMetaToResultDtoAssembler->assemble($budget);
        }

        return $result;
    }
}
