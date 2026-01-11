<?php

declare(strict_types=1);

namespace App\EconumoBundle\Application\Connection\Assembler;

use App\EconumoBundle\Application\Connection\Dto\DeleteInviteV1RequestDto;
use App\EconumoBundle\Application\Connection\Dto\DeleteInviteV1ResultDto;

class DeleteInviteV1ResultAssembler
{
    public function assemble(
        DeleteInviteV1RequestDto $dto
    ): DeleteInviteV1ResultDto {
        return new DeleteInviteV1ResultDto();
    }
}
