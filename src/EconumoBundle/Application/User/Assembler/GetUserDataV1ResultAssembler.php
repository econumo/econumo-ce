<?php

declare(strict_types=1);

namespace App\EconumoBundle\Application\User\Assembler;

use App\EconumoBundle\Application\User\Dto\GetUserDataV1RequestDto;
use App\EconumoBundle\Application\User\Assembler\CurrentUserToDtoResultAssembler;
use App\EconumoBundle\Application\User\Dto\GetUserDataV1ResultDto;
use App\EconumoBundle\Domain\Entity\User;

class GetUserDataV1ResultAssembler
{
    public function __construct(private readonly CurrentUserToDtoResultAssembler $currentUserToDtoResultAssembler)
    {
    }

    public function assemble(
        GetUserDataV1RequestDto $dto,
        User $user
    ): GetUserDataV1ResultDto {
        $result = new GetUserDataV1ResultDto();
        $result->user = $this->currentUserToDtoResultAssembler->assemble($user);

        return $result;
    }
}
