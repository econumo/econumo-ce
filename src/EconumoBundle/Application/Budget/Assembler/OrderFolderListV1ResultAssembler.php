<?php

declare(strict_types=1);

namespace App\EconumoBundle\Application\Budget\Assembler;

use App\EconumoBundle\Application\Budget\Dto\OrderBudgetFolderListV1RequestDto;
use App\EconumoBundle\Application\Budget\Dto\OrderBudgetFolderListV1ResultDto;

readonly class OrderFolderListV1ResultAssembler
{
    public function assemble(
        OrderBudgetFolderListV1RequestDto $dto
    ): OrderBudgetFolderListV1ResultDto {
        return new OrderBudgetFolderListV1ResultDto();
    }
}
