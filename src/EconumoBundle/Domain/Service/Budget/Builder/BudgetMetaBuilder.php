<?php

declare(strict_types=1);

namespace App\EconumoBundle\Domain\Service\Budget\Builder;

use App\EconumoBundle\Domain\Entity\Budget;
use App\EconumoBundle\Domain\Entity\ValueObject\UserName;
use App\EconumoBundle\Domain\Entity\ValueObject\UserRole;
use App\EconumoBundle\Domain\Service\Budget\Dto\BudgetMetaDto;
use App\EconumoBundle\Domain\Service\Budget\Dto\BudgetUserAccessDto;

readonly class BudgetMetaBuilder
{
    public function __construct()
    {
    }

    public function build(Budget $budget): BudgetMetaDto
    {
        /** @var BudgetUserAccessDto[] $accessList */
        $accessList = [];
        foreach ($budget->getAccessList() as $access) {
            $dto = new BudgetUserAccessDto(
                $access->getUserId(),
                new UserName($access->getUser()->getName()),
                $access->getUser()->getAvatarUrl(),
                $access->getRole(),
                $access->isAccepted()
            );
            $accessList[] = $dto;
        }

        $owner = $budget->getUser();
        $accessList[] = new BudgetUserAccessDto(
            $owner->getId(),
            new UserName($owner->getName()),
            $owner->getAvatarUrl(),
            UserRole::owner(),
            true
        );

        return new BudgetMetaDto(
            $budget->getId(),
            $budget->getUser()->getId(),
            $budget->getName(),
            $budget->getStartedAt(),
            $budget->getCurrencyId(),
            $accessList
        );
    }
}