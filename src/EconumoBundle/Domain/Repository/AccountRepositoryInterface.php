<?php

declare(strict_types=1);

namespace App\EconumoBundle\Domain\Repository;

use App\EconumoBundle\Domain\Entity\Account;
use App\EconumoBundle\Domain\Entity\ValueObject\Id;
use DateTimeInterface;

interface AccountRepositoryInterface
{
    public function getNextIdentity(): Id;

    /**
     * @return Account[]
     */
    public function getAvailableForUserId(Id $userId): array;

    /**
     * @return Account[]
     */
    public function getUserAccounts(Id $userId): array;

    /**
     * @return Account[]
     */
    public function getUserAccountsForBudgeting(Id $userId): array;

    public function get(Id $id): Account;

    /**
     * @param Account[] $accounts
     */
    public function save(array $accounts): void;

    public function delete(Id $id): void;

    public function getReference(Id $id): Account;

    /**
     * @param Id[] $accountIds
     * @return array
     */
    public function getAccountsBalancesBeforeDate(array $accountIds, DateTimeInterface $date): array;

    /**
     * @param Id[] $accountIds
     * @return array
     */
    public function getAccountsBalancesOnDate(array $accountIds, DateTimeInterface $date): array;

    /**
     * @param Id[] $accountIds
     * @return array
     */
    public function getAccountsReport(array $accountIds, DateTimeInterface $periodStart, DateTimeInterface $periodEnd): array;

    /**
     * @param Id[] $reportAccountIds
     * @return array
     */
    public function getHoldingsReport(array $reportAccountIds, DateTimeInterface $periodStart, DateTimeInterface $periodEnd): array;

    /**
     * @param Id[] $userIds
     * @return Account[]
     */
    public function findByOwnersIds(array $userIds): array;
}
