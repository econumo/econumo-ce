<?php

declare(strict_types=1);

namespace App\EconumoBundle\Application\Payee\Assembler;

use App\EconumoBundle\Application\Payee\Dto\DeletePayeeV1RequestDto;
use App\EconumoBundle\Application\Payee\Dto\DeletePayeeV1ResultDto;

class DeletePayeeV1ResultAssembler
{
    public function assemble(
        DeletePayeeV1RequestDto $dto
    ): DeletePayeeV1ResultDto {
        return new DeletePayeeV1ResultDto();
    }
}
