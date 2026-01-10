<?php

declare(strict_types=1);


namespace App\EconumoBundle\Domain\Service\Analytics;

use App\EconumoBundle\Domain\Entity\ValueObject\Id;
use App\EconumoBundle\Domain\Entity\ValueObject\DecimalNumber;
use App\EconumoBundle\Domain\Repository\AccountRepositoryInterface;
use App\EconumoBundle\Domain\Repository\TransactionRepositoryInterface;
use App\EconumoBundle\Domain\Repository\UserRepositoryInterface;
use App\EconumoBundle\Domain\Service\Currency\CurrencyConvertorInterface;
use App\EconumoBundle\Domain\Service\Dto\BalanceAnalyticsDto;
use DateInterval;
use DatePeriod;
use DateTimeInterface;

readonly class BalanceAnalyticsService implements BalanceAnalyticsServiceInterface
{
    public function __construct(
        private AccountRepositoryInterface $accountRepository,
        private TransactionRepositoryInterface $transactionRepository,
        private CurrencyConvertorInterface $currencyConvertor,
        private UserRepositoryInterface $userRepository
    ) {
    }

    /**
     * @inheritDoc
     */
    public function getBalanceAnalytics(DateTimeInterface $from, DateTimeInterface $to, Id $userId): array
    {
        $result = [];
        $accounts = $this->accountRepository->getUserAccounts($userId);
        $user = $this->userRepository->get($userId);

        foreach (new DatePeriod($from, new DateInterval('P1M'), $to) as $date) {
            $balance = new DecimalNumber();
            foreach ($accounts as $account) {
                if ($account->isDeleted()) {
                    continue;
                }

                if (!$account->getUserId()->isEqual($userId)) {
                    continue;
                }

                $accountBalance = $this->transactionRepository->getAccountBalance($account->getId(), $date);
                $accountBalanceConverted = $this->currencyConvertor->convert(
                    $account->getCurrencyCode(),
                    $user->getCurrency(),
                    $accountBalance
                );
                $balance = $balance->add($accountBalanceConverted);
            }

            $item = new BalanceAnalyticsDto();
            $item->balance = $balance;
            $item->date = $date;
            $result[] = $item;
        }

        return $result;
    }
}
