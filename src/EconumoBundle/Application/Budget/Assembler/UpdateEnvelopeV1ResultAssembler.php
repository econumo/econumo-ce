<?php

declare(strict_types=1);

namespace App\EconumoBundle\Application\Budget\Assembler;

use App\EconumoBundle\Application\Budget\Dto\UpdateEnvelopeV1ResultDto;
use App\EconumoBundle\Domain\Service\Budget\Dto\BudgetStructureParentElementDto;

readonly class UpdateEnvelopeV1ResultAssembler
{
    public function __construct(
        private BudgetParentElementToResultDtoAssembler $budgetParentElementToResultDtoAssembler
    ) {
    }

    public function assemble(
        BudgetStructureParentElementDto $dto
    ): UpdateEnvelopeV1ResultDto {
        $result = new UpdateEnvelopeV1ResultDto();
        $result->item = $this->budgetParentElementToResultDtoAssembler->assemble($dto);

        return $result;
    }
}
