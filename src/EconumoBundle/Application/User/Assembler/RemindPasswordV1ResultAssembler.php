<?php

declare(strict_types=1);

namespace App\EconumoBundle\Application\User\Assembler;

use App\EconumoBundle\Application\User\Dto\RemindPasswordV1RequestDto;
use App\EconumoBundle\Application\User\Dto\RemindPasswordV1ResultDto;

class RemindPasswordV1ResultAssembler
{
    public function assemble(
        RemindPasswordV1RequestDto $dto
    ): RemindPasswordV1ResultDto {
        return new RemindPasswordV1ResultDto();
    }
}
