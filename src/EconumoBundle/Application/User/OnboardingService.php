<?php

declare(strict_types=1);

namespace App\EconumoBundle\Application\User;

use App\EconumoBundle\Application\User\Dto\CompleteOnboardingV1RequestDto;
use App\EconumoBundle\Application\User\Dto\CompleteOnboardingV1ResultDto;
use App\EconumoBundle\Application\User\Assembler\CompleteOnboardingV1ResultAssembler;
use App\EconumoBundle\Domain\Entity\ValueObject\Id;
use App\EconumoBundle\Domain\Service\UserServiceInterface;

readonly class OnboardingService
{
    public function __construct(
        private CompleteOnboardingV1ResultAssembler $completeOnboardingV1ResultAssembler,
        private UserServiceInterface $userService
    ) {
    }

    public function completeOnboarding(
        CompleteOnboardingV1RequestDto $dto,
        Id $userId
    ): CompleteOnboardingV1ResultDto {
        $this->userService->completeOnboarding($userId);
        return $this->completeOnboardingV1ResultAssembler->assemble($userId);
    }
}
