<?php

declare(strict_types=1);

namespace App\EconumoBundle\Application\User\Assembler;

use App\EconumoBundle\Application\User\Assembler\CurrentUserToDtoResultAssembler;
use App\EconumoBundle\Application\User\Dto\LoginUserV1ResultDto;
use App\EconumoBundle\Domain\Entity\User;

class LoginUserV1ResultAssembler
{
    public function __construct(private readonly CurrentUserToDtoResultAssembler $currentUserToDtoResultAssembler)
    {
    }

    public function assemble(
        User $user,
        string $token
    ): LoginUserV1ResultDto {
        $result = new LoginUserV1ResultDto();
        $result->token = $token;
        $result->user = $this->currentUserToDtoResultAssembler->assemble($user);

        return $result;
    }
}
