<?php

declare(strict_types=1);

namespace App\EconumoBundle\Application\Budget\Assembler;

use App\EconumoBundle\Application\Budget\Dto\RevokeAccessV1ResultDto;

readonly class RevokeAccessV1ResultAssembler
{
    public function assemble(): RevokeAccessV1ResultDto
    {
        return new RevokeAccessV1ResultDto();
    }
}
