<?php

declare(strict_types=1);


namespace App\EconumoBundle\Domain\Service\Budget\Assembler;


use App\EconumoBundle\Domain\Entity\ValueObject\Id;
use App\EconumoBundle\Domain\Entity\ValueObject\DecimalNumber;
use App\EconumoBundle\Domain\Repository\AccountRepositoryInterface;
use App\EconumoBundle\Domain\Service\Budget\Dto\CurrencyBalanceDto;
use App\EconumoBundle\Domain\Service\DatetimeServiceInterface;
use DateTimeInterface;

readonly class CurrencyBalanceDtoAssembler
{
    public function __construct(
        private AccountRepositoryInterface $accountRepository,
        private DatetimeServiceInterface $datetimeService,
    ) {
    }

    /**
     * @param Id[] $includedAccountsIds
     * @param Id[] $currenciesIds
     * @return CurrencyBalanceDto[]
     */
    public function assemble(
        DateTimeInterface $periodStart,
        DateTimeInterface $periodEnd,
        array $includedAccountsIds,
        array $currenciesIds
    ): array {
        $now = $this->datetimeService->getCurrentDatetime();
        $startBalances = [];
        if ($periodStart <= $now) {
            $startBalances = $this->accountRepository->getAccountsBalancesOnDate($includedAccountsIds, $periodStart);
        }

        $endBalances = [];
        if ($periodEnd <= $now) {
            $endBalances = $this->accountRepository->getAccountsBalancesBeforeDate($includedAccountsIds, $periodEnd);
        }

        $reports = [];
        if ($periodStart <= $now) {
            $reports = $this->accountRepository->getAccountsReport($includedAccountsIds, $periodStart, $periodEnd);
        }

        $holdingsReports = [];
        if ($periodStart <= $now) {
            $holdingsReports = $this->accountRepository->getHoldingsReport($includedAccountsIds, $periodStart, $periodEnd);
        }

        $result = [];
        foreach ($currenciesIds as $currencyId) {
            $startBalance = $this->summarize($startBalances, $currencyId, 'balance');
            $endBalance = $this->summarize($endBalances, $currencyId, 'balance');
            $income = $this->summarize($reports, $currencyId, 'incomes');
            $expenses = $this->summarize($reports, $currencyId, 'expenses');
            $exchanges = $this->summarize($reports, $currencyId, 'exchange_incomes')->sub(
                $this->summarize(
                    $reports,
                    $currencyId,
                    'exchange_expenses'
                )
            );
            $holdings = new DecimalNumber();
            if (array_key_exists($currencyId->getValue(), $holdingsReports)) {
                $holdings = (new DecimalNumber($holdingsReports[$currencyId->getValue()]['from_holdings']))->sub(
                    new DecimalNumber($holdingsReports[$currencyId->getValue()]['to_holdings'])
                );
            }

            $item = new CurrencyBalanceDto(
                $currencyId,
                ($periodStart <= $now ? $startBalance : null),
                ($periodEnd <= $now ? $endBalance : null),
                ($periodStart <= $now ? $income : null),
                ($periodStart <= $now ? $expenses : null),
                ($periodStart <= $now ? $exchanges : null),
                $holdings
            );
            $result[] = $item;
        }

        return $result;
    }

    private function summarize(array $items, Id $currencyId, string $field): DecimalNumber
    {
        $result = new DecimalNumber();
        foreach ($items as $item) {
            if ($item['currency_id'] === $currencyId->getValue()) {
                $result = $result->add($item[$field]);
            }
        }

        return $result;
    }
}
