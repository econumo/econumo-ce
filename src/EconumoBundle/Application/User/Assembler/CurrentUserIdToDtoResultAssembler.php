<?php

declare(strict_types=1);

namespace App\EconumoBundle\Application\User\Assembler;

use App\EconumoBundle\Application\User\Assembler\CurrentUserToDtoResultAssembler;
use App\EconumoBundle\Application\User\Dto\CurrentUserResultDto;
use App\EconumoBundle\Domain\Entity\ValueObject\Id;
use App\EconumoBundle\Domain\Repository\UserRepositoryInterface;

class CurrentUserIdToDtoResultAssembler
{
    public function __construct(private readonly UserRepositoryInterface $userRepository, private readonly CurrentUserToDtoResultAssembler $currentUserToDtoResultAssembler)
    {
    }

    public function assemble(Id $userId): CurrentUserResultDto
    {
        $user = $this->userRepository->get($userId);
        return $this->currentUserToDtoResultAssembler->assemble($user);
    }
}
