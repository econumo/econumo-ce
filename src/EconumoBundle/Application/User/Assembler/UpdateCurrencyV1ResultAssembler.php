<?php

declare(strict_types=1);

namespace App\EconumoBundle\Application\User\Assembler;

use App\EconumoBundle\Application\User\Dto\UpdateCurrencyV1RequestDto;
use App\EconumoBundle\Application\User\Assembler\CurrentUserToDtoResultAssembler;
use App\EconumoBundle\Application\User\Dto\UpdateCurrencyV1ResultDto;
use App\EconumoBundle\Domain\Entity\User;

class UpdateCurrencyV1ResultAssembler
{
    public function __construct(private readonly CurrentUserToDtoResultAssembler $currentUserToDtoResultAssembler)
    {
    }

    public function assemble(
        UpdateCurrencyV1RequestDto $dto,
        User $user
    ): UpdateCurrencyV1ResultDto {
        $result = new UpdateCurrencyV1ResultDto();
        $result->user = $this->currentUserToDtoResultAssembler->assemble($user);

        return $result;
    }
}
