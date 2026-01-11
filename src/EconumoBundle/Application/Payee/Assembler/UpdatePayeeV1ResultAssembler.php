<?php

declare(strict_types=1);

namespace App\EconumoBundle\Application\Payee\Assembler;

use App\EconumoBundle\Application\Payee\Dto\UpdatePayeeV1RequestDto;
use App\EconumoBundle\Application\Payee\Dto\UpdatePayeeV1ResultDto;
use App\EconumoBundle\Domain\Entity\ValueObject\Id;

class UpdatePayeeV1ResultAssembler
{
    public function assemble(
        UpdatePayeeV1RequestDto $dto
    ): UpdatePayeeV1ResultDto {
        return new UpdatePayeeV1ResultDto();
    }
}
