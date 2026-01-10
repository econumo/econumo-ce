<?php

declare(strict_types=1);

namespace App\EconumoBundle\Application\Account\Assembler;

use App\EconumoBundle\Application\Account\Dto\ShowFolderV1RequestDto;
use App\EconumoBundle\Application\Account\Dto\ShowFolderV1ResultDto;

class ShowFolderV1ResultAssembler
{
    public function assemble(
        ShowFolderV1RequestDto $dto
    ): ShowFolderV1ResultDto {
        return new ShowFolderV1ResultDto();
    }
}
