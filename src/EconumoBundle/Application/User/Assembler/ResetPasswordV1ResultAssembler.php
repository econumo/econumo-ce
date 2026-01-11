<?php

declare(strict_types=1);

namespace App\EconumoBundle\Application\User\Assembler;

use App\EconumoBundle\Application\User\Dto\ResetPasswordV1RequestDto;
use App\EconumoBundle\Application\User\Dto\ResetPasswordV1ResultDto;

readonly class ResetPasswordV1ResultAssembler
{
    public function assemble(
        ResetPasswordV1RequestDto $dto
    ): ResetPasswordV1ResultDto {
        return new ResetPasswordV1ResultDto();
    }
}
