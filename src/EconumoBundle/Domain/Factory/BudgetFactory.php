<?php

declare(strict_types=1);


namespace App\EconumoBundle\Domain\Factory;


use App\EconumoBundle\Domain\Entity\Budget;
use App\EconumoBundle\Domain\Entity\ValueObject\BudgetName;
use App\EconumoBundle\Domain\Entity\ValueObject\Id;
use App\EconumoBundle\Domain\Factory\BudgetFactoryInterface;
use App\EconumoBundle\Domain\Repository\AccountRepositoryInterface;
use App\EconumoBundle\Domain\Repository\CurrencyRepositoryInterface;
use App\EconumoBundle\Domain\Repository\UserRepositoryInterface;
use App\EconumoBundle\Domain\Service\DatetimeServiceInterface;
use DateTimeInterface;

readonly class BudgetFactory implements BudgetFactoryInterface
{
    public function __construct(
        private DatetimeServiceInterface $datetimeService,
        private AccountRepositoryInterface $accountRepository,
        private UserRepositoryInterface $userRepository,
        private CurrencyRepositoryInterface $currencyRepository,
    ) {
    }

    public function create(
        Id $userId,
        Id $id,
        BudgetName $name,
        DateTimeInterface $startDate,
        Id $currencyId,
        array $excludedAccountsIds
    ): Budget {
        $accounts = [];
        foreach ($excludedAccountsIds as $excludedAccountId) {
            $account = $this->accountRepository->getReference($excludedAccountId);
            $accounts[] = $account;
        }

        $currency = $this->currencyRepository->getReference($currencyId);
        return new Budget(
            $this->userRepository->getReference($userId),
            $id,
            $name,
            $currency,
            $accounts,
            $startDate,
            $this->datetimeService->getCurrentDatetime()
        );
    }
}
