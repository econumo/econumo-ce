<?php

declare(strict_types=1);

namespace App\EconumoBundle\Application\Budget\Assembler;

use App\EconumoBundle\Application\Budget\Dto\SetLimitV1ResultDto;

readonly class SetLimitV1ResultAssembler
{
    public function assemble(): SetLimitV1ResultDto
    {
        return new SetLimitV1ResultDto();
    }
}
