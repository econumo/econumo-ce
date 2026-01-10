<?php

declare(strict_types=1);

namespace App\EconumoBundle\Application\Budget\Assembler;

use App\EconumoBundle\Application\Budget\Dto\DeclineAccessV1ResultDto;

readonly class DeclineAccessV1ResultAssembler
{
    public function assemble(): DeclineAccessV1ResultDto {
        return new DeclineAccessV1ResultDto();
    }
}
