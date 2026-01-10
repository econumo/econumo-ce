<?php

declare(strict_types=1);

namespace App\EconumoBundle\Application\User\Assembler;

use App\EconumoBundle\Application\User\Dto\UpdateUserBudgetV1RequestDto;
use App\EconumoBundle\Application\User\Dto\UpdateUserBudgetV1ResultDto;
use App\EconumoBundle\Application\User\Assembler\CurrentUserToDtoResultAssembler;
use App\EconumoBundle\Domain\Entity\User;

readonly class UpdateBudgetV1ResultAssembler
{
    public function __construct(private CurrentUserToDtoResultAssembler $currentUserToDtoResultAssembler)
    {
    }

    public function assemble(
        UpdateUserBudgetV1RequestDto $dto,
        User $user
    ): UpdateUserBudgetV1ResultDto {
        $result = new UpdateUserBudgetV1ResultDto();
        $result->user = $this->currentUserToDtoResultAssembler->assemble($user);

        return $result;
    }
}
