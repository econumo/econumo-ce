<?php

declare(strict_types=1);

namespace App\EconumoBundle\Application\Payee\Assembler;

use App\EconumoBundle\Application\Payee\Dto\UnarchivePayeeV1RequestDto;
use App\EconumoBundle\Application\Payee\Dto\UnarchivePayeeV1ResultDto;

class UnarchivePayeeV1ResultAssembler
{
    public function assemble(
        UnarchivePayeeV1RequestDto $dto
    ): UnarchivePayeeV1ResultDto {
        return new UnarchivePayeeV1ResultDto();
    }
}
