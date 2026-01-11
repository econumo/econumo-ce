<?php

declare(strict_types=1);

namespace App\EconumoBundle\Application\Budget\Assembler;

use App\EconumoBundle\Application\Budget\Dto\DeleteBudgetV1RequestDto;
use App\EconumoBundle\Application\Budget\Dto\DeleteBudgetV1ResultDto;

readonly class DeleteBudgetV1ResultAssembler
{
    public function assemble(
    ): DeleteBudgetV1ResultDto {
        return new DeleteBudgetV1ResultDto();
    }
}
