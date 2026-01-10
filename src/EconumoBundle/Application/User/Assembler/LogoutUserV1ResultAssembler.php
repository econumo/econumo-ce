<?php

declare(strict_types=1);

namespace App\EconumoBundle\Application\User\Assembler;

use App\EconumoBundle\Application\User\Dto\LogoutUserV1RequestDto;
use App\EconumoBundle\Application\User\Dto\LogoutUserV1ResultDto;

class LogoutUserV1ResultAssembler
{
    public function assemble(
        LogoutUserV1RequestDto $dto
    ): LogoutUserV1ResultDto {
        $result = new LogoutUserV1ResultDto();
        $result->result = 'test';

        return $result;
    }
}
