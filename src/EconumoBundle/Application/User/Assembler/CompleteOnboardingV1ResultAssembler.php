<?php

declare(strict_types=1);

namespace App\EconumoBundle\Application\User\Assembler;

use App\EconumoBundle\Application\User\Dto\CompleteOnboardingV1ResultDto;
use App\EconumoBundle\Domain\Entity\ValueObject\Id;

readonly class CompleteOnboardingV1ResultAssembler
{
    public function __construct(
        private CurrentUserIdToDtoResultAssembler $currentUserIdToDtoResultAssembler
    ) {
    }

    public function assemble(Id $userId): CompleteOnboardingV1ResultDto
    {
        $result = new CompleteOnboardingV1ResultDto();
        $result->user = $this->currentUserIdToDtoResultAssembler->assemble($userId);

        return $result;
    }
}
