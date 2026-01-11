<?php

declare(strict_types=1);


namespace App\EconumoBundle\Application\Connection\Assembler;


use App\EconumoBundle\Application\Connection\Dto\ConnectionInviteResultDto;
use App\EconumoBundle\Domain\Entity\ConnectionInvite;

class ConnectionInviteToDtoResultAssembler
{
    public function assemble(ConnectionInvite $connectionInvite): ConnectionInviteResultDto
    {
        $dto = new ConnectionInviteResultDto();
        $dto->code = $connectionInvite->getCode()->getValue();
        $dto->expiredAt = $connectionInvite->getExpiredAt()->format('Y-m-d H:i:s');

        return $dto;
    }
}
