<?php

declare(strict_types=1);

namespace App\EconumoBundle\Domain\Repository;

use App\EconumoBundle\Domain\Entity\Transaction;
use App\EconumoBundle\Domain\Entity\ValueObject\Id;
use App\EconumoBundle\Domain\Entity\ValueObject\DecimalNumber;
use DateTimeInterface;

interface TransactionRepositoryInterface
{
    public function getNextIdentity(): Id;

    /**
     * @return Transaction[]
     */
    public function findByAccountId(Id $accountId): array;

    public function getAccountBalance(Id $accountId, DateTimeInterface $date): DecimalNumber;

    /**
     * @param Transaction[] $transactions
     */
    public function save(array $transactions): void;

    /**
     * @param Id[] $excludeAccounts
     * @return Transaction[]
     */
    public function findAvailableForUserId(
        Id $userId,
        array $excludeAccounts = [],
        DateTimeInterface $periodStart = null,
        DateTimeInterface $periodEnd = null
    ): array;

    /**
     * @return Transaction[]
     */
    public function findChanges(Id $userId, DateTimeInterface $lastUpdate): array;

    public function get(Id $id): Transaction;

    public function delete(Transaction $transaction): void;

    public function replaceCategory(Id $oldCategoryId, Id $newCategoryId): void;

    /**
     * @param Id[] $categoryIds
     * @param Id[] $accountsIds
     * @return array
     */
    public function countSpendingForCategories(
        array $categoryIds,
        array $accountsIds,
        DateTimeInterface $startDate,
        DateTimeInterface $endDate
    ): array;

    /**
     * @param Id[] $tagsIds
     * @param Id[] $accountsIds
     * @return array
     */
    public function countSpendingForTags(
        array $tagsIds,
        array $accountsIds,
        DateTimeInterface $startDate,
        DateTimeInterface $endDate
    ): array;

    /**
     * @param Id[] $categoriesIds
     * @param Id[] $accountsIds
     * @return array
     */
    public function countSpending(
        array $categoriesIds,
        array $accountsIds,
        DateTimeInterface $startDate,
        DateTimeInterface $endDate
    ): array;

    /**
     * @param Id[] $accountIds
     * @return Transaction[]
     */
    public function getByCategoriesIdsForAccountsIds(
        array $categoriesIds,
        DateTimeInterface $periodStart,
        DateTimeInterface $periodEnd,
        array $accountIds
    ): array;

    /**
     * @param Id[] $accountIds
     * @return Transaction[]
     */
    public function getByTagsIdsForAccountsIds(
        array $tagIds,
        DateTimeInterface $periodStart,
        DateTimeInterface $periodEnd,
        array $accountIds,
        array $categoriesIds
    ): array;
}
