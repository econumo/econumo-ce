<?php

declare(strict_types=1);

namespace App\EconumoBundle\Application\User;

use App\EconumoBundle\Application\User\Dto\GetUserDataV1RequestDto;
use App\EconumoBundle\Application\User\Dto\GetUserDataV1ResultDto;
use App\EconumoBundle\Application\User\Assembler\GetUserDataV1ResultAssembler;
use App\EconumoBundle\Domain\Entity\ValueObject\Id;
use App\EconumoBundle\Domain\Repository\UserRepositoryInterface;

class UserDataService
{
    public function __construct(private readonly GetUserDataV1ResultAssembler $getUserDataV1ResultAssembler, private readonly UserRepositoryInterface $userRepository)
    {
    }

    public function getUserData(
        GetUserDataV1RequestDto $dto,
        Id $userId
    ): GetUserDataV1ResultDto {
        $user = $this->userRepository->get($userId);
        return $this->getUserDataV1ResultAssembler->assemble($dto, $user);
    }
}
