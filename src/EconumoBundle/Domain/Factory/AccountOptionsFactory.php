<?php

declare(strict_types=1);

namespace App\EconumoBundle\Domain\Factory;

use App\EconumoBundle\Domain\Entity\AccountOptions;
use App\EconumoBundle\Domain\Entity\ValueObject\Id;
use App\EconumoBundle\Domain\Repository\AccountRepositoryInterface;
use App\EconumoBundle\Domain\Repository\UserRepositoryInterface;
use App\EconumoBundle\Domain\Service\DatetimeServiceInterface;
use App\EconumoBundle\Domain\Factory\AccountOptionsFactoryInterface;

class AccountOptionsFactory implements AccountOptionsFactoryInterface
{
    public function __construct(private readonly AccountRepositoryInterface $accountRepository, private readonly UserRepositoryInterface $userRepository, private readonly DatetimeServiceInterface $datetimeService)
    {
    }

    public function create(
        Id $accountId,
        Id $userId,
        int $position
    ): AccountOptions {
        return new AccountOptions(
            $this->accountRepository->getReference($accountId),
            $this->userRepository->getReference($userId),
            $position,
            $this->datetimeService->getCurrentDatetime()
        );
    }
}
