<?php

declare(strict_types=1);

namespace App\EconumoBundle\Domain\Factory;

use App\EconumoBundle\Domain\Entity\Account;
use App\EconumoBundle\Domain\Entity\ValueObject\AccountName;
use App\EconumoBundle\Domain\Entity\ValueObject\AccountType;
use App\EconumoBundle\Domain\Entity\ValueObject\Icon;
use App\EconumoBundle\Domain\Entity\ValueObject\Id;
use App\EconumoBundle\Domain\Factory\AccountFactoryInterface;
use App\EconumoBundle\Domain\Repository\AccountRepositoryInterface;
use App\EconumoBundle\Domain\Repository\CurrencyRepositoryInterface;
use App\EconumoBundle\Domain\Repository\UserRepositoryInterface;
use App\EconumoBundle\Domain\Service\DatetimeServiceInterface;

class AccountFactory implements AccountFactoryInterface
{
    public function __construct(private readonly AccountRepositoryInterface $accountRepository, private readonly DatetimeServiceInterface $datetimeService, private readonly CurrencyRepositoryInterface $currencyRepository, private readonly UserRepositoryInterface $userRepository)
    {
    }

    public function create(
        Id $userId,
        AccountName $name,
        AccountType $accountType,
        Id $currencyId,
        Icon $icon
    ): Account {
        return new Account(
            $this->accountRepository->getNextIdentity(),
            $this->userRepository->getReference($userId),
            $name,
            $this->currencyRepository->getReference($currencyId),
            $accountType,
            $icon,
            $this->datetimeService->getCurrentDatetime()
        );
    }
}
