<?php

declare(strict_types=1);

namespace App\EconumoBundle\Application\Payee\Assembler;

use App\EconumoBundle\Application\Payee\Dto\ArchivePayeeV1RequestDto;
use App\EconumoBundle\Application\Payee\Dto\ArchivePayeeV1ResultDto;

class ArchivePayeeV1ResultAssembler
{
    public function assemble(
        ArchivePayeeV1RequestDto $dto
    ): ArchivePayeeV1ResultDto {
        return new ArchivePayeeV1ResultDto();
    }
}
