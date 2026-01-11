<?php

declare(strict_types=1);

namespace App\EconumoBundle\Application\Budget\Assembler;

use App\EconumoBundle\Application\Budget\Dto\DeleteEnvelopeV1ResultDto;

readonly class DeleteEnvelopeV1ResultAssembler
{
    public function assemble(): DeleteEnvelopeV1ResultDto
    {
        return new DeleteEnvelopeV1ResultDto();
    }
}
