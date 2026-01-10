<?php

declare(strict_types=1);

namespace App\EconumoBundle\Application\User\Assembler;

use App\EconumoBundle\Application\User\Assembler\UserToDtoResultAssembler;
use App\EconumoBundle\Application\User\Dto\UserResultDto;
use App\EconumoBundle\Domain\Entity\ValueObject\Id;
use App\EconumoBundle\Domain\Repository\UserRepositoryInterface;

class UserIdToDtoResultAssembler
{
    public function __construct(private readonly UserRepositoryInterface $userRepository, private readonly UserToDtoResultAssembler $userToDtoResultAssembler)
    {
    }

    public function assemble(Id $userId): UserResultDto
    {
        $user = $this->userRepository->get($userId);
        return $this->userToDtoResultAssembler->assemble($user);
    }
}
