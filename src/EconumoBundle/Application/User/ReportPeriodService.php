<?php

declare(strict_types=1);

namespace App\EconumoBundle\Application\User;

use App\EconumoBundle\Application\User\Dto\UpdateReportPeriodV1RequestDto;
use App\EconumoBundle\Application\User\Dto\UpdateReportPeriodV1ResultDto;
use App\EconumoBundle\Application\User\Assembler\UpdateReportPeriodV1ResultAssembler;
use App\EconumoBundle\Domain\Entity\ValueObject\Id;
use App\EconumoBundle\Domain\Entity\ValueObject\ReportPeriod;
use App\EconumoBundle\Domain\Repository\UserRepositoryInterface;
use App\EconumoBundle\Domain\Service\UserServiceInterface;

class ReportPeriodService
{
    public function __construct(private readonly UpdateReportPeriodV1ResultAssembler $updateReportPeriodV1ResultAssembler, private readonly UserServiceInterface $userService, private readonly UserRepositoryInterface $userRepository)
    {
    }

    public function updateReportPeriod(
        UpdateReportPeriodV1RequestDto $dto,
        Id $userId
    ): UpdateReportPeriodV1ResultDto {
        $this->userService->updateReportPeriod($userId, new ReportPeriod($dto->value));
        $user = $this->userRepository->get($userId);
        return $this->updateReportPeriodV1ResultAssembler->assemble($dto, $user);
    }
}
