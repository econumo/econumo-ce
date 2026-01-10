<?php

declare(strict_types=1);

namespace App\EconumoBundle\Application\Budget\Assembler;

use App\EconumoBundle\Application\Budget\Dto\ChangeElementCurrencyV1ResultDto;

readonly class ChangeElementCurrencyV1ResultAssembler
{
    public function assemble(): ChangeElementCurrencyV1ResultDto {
        return new ChangeElementCurrencyV1ResultDto();
    }
}
