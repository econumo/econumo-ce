<?php

declare(strict_types=1);


namespace App\EconumoBundle\Domain\Factory;


use App\EconumoBundle\Domain\Entity\BudgetAccess;
use App\EconumoBundle\Domain\Entity\ValueObject\BudgetUserRole;
use App\EconumoBundle\Domain\Entity\ValueObject\Id;
use App\EconumoBundle\Domain\Entity\ValueObject\UserRole;
use App\EconumoBundle\Domain\Repository\BudgetRepositoryInterface;
use App\EconumoBundle\Domain\Repository\UserRepositoryInterface;
use App\EconumoBundle\Domain\Service\DatetimeServiceInterface;

readonly class BudgetAccessFactory implements BudgetAccessFactoryInterface
{
    public function __construct(
        private DatetimeServiceInterface $datetimeService,
        private BudgetRepositoryInterface $budgetRepository,
        private UserRepositoryInterface $userRepository,
    ) {
    }

    public function create(Id $budgetId, Id $userId, BudgetUserRole $role): BudgetAccess
    {
        return new BudgetAccess(
            $this->budgetRepository->getReference($budgetId),
            $this->userRepository->getReference($userId),
            UserRole::createFromAlias($role->getAlias()),
            $this->datetimeService->getCurrentDatetime()
        );
    }
}
