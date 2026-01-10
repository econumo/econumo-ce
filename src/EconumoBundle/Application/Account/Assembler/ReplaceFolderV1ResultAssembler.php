<?php

declare(strict_types=1);

namespace App\EconumoBundle\Application\Account\Assembler;

use App\EconumoBundle\Application\Account\Dto\ReplaceFolderV1RequestDto;
use App\EconumoBundle\Application\Account\Dto\ReplaceFolderV1ResultDto;

class ReplaceFolderV1ResultAssembler
{
    public function assemble(
        ReplaceFolderV1RequestDto $dto
    ): ReplaceFolderV1ResultDto {
        return new ReplaceFolderV1ResultDto();
    }
}
