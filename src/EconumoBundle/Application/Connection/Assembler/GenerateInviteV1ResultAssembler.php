<?php

declare(strict_types=1);

namespace App\EconumoBundle\Application\Connection\Assembler;

use App\EconumoBundle\Application\Connection\Dto\GenerateInviteV1RequestDto;
use App\EconumoBundle\Application\Connection\Dto\GenerateInviteV1ResultDto;
use App\EconumoBundle\Application\Connection\Assembler\ConnectionInviteToDtoResultAssembler;
use App\EconumoBundle\Domain\Entity\ConnectionInvite;

class GenerateInviteV1ResultAssembler
{
    public function __construct(private readonly ConnectionInviteToDtoResultAssembler $connectionInviteToDtoResultAssembler)
    {
    }

    public function assemble(
        GenerateInviteV1RequestDto $dto,
        ConnectionInvite $connectionInvite
    ): GenerateInviteV1ResultDto {
        $result = new GenerateInviteV1ResultDto();
        $result->item = $this->connectionInviteToDtoResultAssembler->assemble($connectionInvite);

        return $result;
    }
}
