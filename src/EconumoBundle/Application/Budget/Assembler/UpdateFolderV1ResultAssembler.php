<?php

declare(strict_types=1);

namespace App\EconumoBundle\Application\Budget\Assembler;

use App\EconumoBundle\Application\Budget\Dto\UpdateBudgetFolderV1ResultDto;
use App\EconumoBundle\Domain\Service\Budget\Dto\BudgetStructureFolderDto;

readonly class UpdateFolderV1ResultAssembler
{
    public function __construct(
        private BudgetFolderToResultDtoAssembler $budgetFolderToResultDtoAssembler,
    ) {
    }

    public function assemble(
        BudgetStructureFolderDto $folder
    ): UpdateBudgetFolderV1ResultDto {
        $result = new UpdateBudgetFolderV1ResultDto();
        $result->item = $this->budgetFolderToResultDtoAssembler->assemble($folder);

        return $result;
    }
}
