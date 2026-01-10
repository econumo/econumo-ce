<?php

declare(strict_types=1);

namespace App\EconumoBundle\Application\User\Assembler;

use App\EconumoBundle\Application\User\Dto\UserResultDto;
use App\EconumoBundle\Domain\Entity\User;

class UserToDtoResultAssembler
{
    public function assemble(User $user): UserResultDto
    {
        $dto = new UserResultDto();
        $dto->id = $user->getId()->getValue();
        $dto->name = $user->getName();
        $dto->avatar = $user->getAvatarUrl();

        return $dto;
    }
}
