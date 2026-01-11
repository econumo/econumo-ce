<?php

declare(strict_types=1);

namespace App\EconumoBundle\Application\Budget\Assembler;

use App\EconumoBundle\Application\Budget\Dto\MoveElementListV1RequestDto;
use App\EconumoBundle\Application\Budget\Dto\MoveElementListV1ResultDto;

readonly class MoveElementListV1ResultAssembler
{
    public function assemble(
        MoveElementListV1RequestDto $dto
    ): MoveElementListV1ResultDto {
        return new MoveElementListV1ResultDto();
    }
}
