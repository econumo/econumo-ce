<?php

declare(strict_types=1);

namespace App\EconumoBundle\Application\Connection\Assembler;

use App\EconumoBundle\Application\Connection\Dto\RevokeAccountAccessV1RequestDto;
use App\EconumoBundle\Application\Connection\Dto\RevokeAccountAccessV1ResultDto;

class RevokeAccountAccessV1ResultAssembler
{
    public function assemble(
        RevokeAccountAccessV1RequestDto $dto
    ): RevokeAccountAccessV1ResultDto {
        return new RevokeAccountAccessV1ResultDto();
    }
}
