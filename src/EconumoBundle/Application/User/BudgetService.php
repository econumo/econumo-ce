<?php

declare(strict_types=1);

namespace App\EconumoBundle\Application\User;

use App\EconumoBundle\Application\Exception\ValidationException;
use App\EconumoBundle\Application\User\Dto\UpdateUserBudgetV1RequestDto;
use App\EconumoBundle\Application\User\Dto\UpdateUserBudgetV1ResultDto;
use App\EconumoBundle\Application\User\Assembler\UpdateBudgetV1ResultAssembler;
use App\EconumoBundle\Domain\Entity\ValueObject\Id;
use App\EconumoBundle\Domain\Exception\NotFoundException;
use App\EconumoBundle\Domain\Repository\UserRepositoryInterface;
use App\EconumoBundle\Domain\Service\UserServiceInterface;

readonly class BudgetService
{
    public function __construct(
        private UpdateBudgetV1ResultAssembler $updateBudgetV1ResultAssembler,
        private UserServiceInterface $userService,
        private UserRepositoryInterface $userRepository
    ) {
    }

    public function updateBudget(
        UpdateUserBudgetV1RequestDto $dto,
        Id $userId
    ): UpdateUserBudgetV1ResultDto {
        try {
            $this->userService->updateBudget($userId, new Id($dto->value));
            $user = $this->userRepository->get($userId);
            return $this->updateBudgetV1ResultAssembler->assemble($dto, $user);
        } catch (NotFoundException) {
            throw new ValidationException('Plan not found');
        }
    }
}
