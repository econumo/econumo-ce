<?php

declare(strict_types=1);


namespace App\EconumoBundle\Domain\Service\Budget;

use App\EconumoBundle\Domain\Entity\Transaction;
use App\EconumoBundle\Domain\Entity\ValueObject\Id;
use DateTimeInterface;

interface BudgetTransactionServiceInterface
{
    /**
     * @param Id $userId
     * @param Id $budgetId
     * @param DateTimeInterface $periodStart
     * @param Id $categoryId
     * @return Transaction[]
     */
    public function getTransactionsForCategory(Id $userId, Id $budgetId, DateTimeInterface $periodStart, Id $categoryId): array;

    /**
     * @param Id $userId
     * @param Id $budgetId
     * @param DateTimeInterface $periodStart
     * @param Id $tagId
     * @param Id|null $categoryId
     * @return Transaction[]
     */
    public function getTransactionsForTag(Id $userId, Id $budgetId, DateTimeInterface $periodStart, Id $tagId, ?Id $categoryId): array;

    /**
     * @param Id $userId
     * @param Id $budgetId
     * @param DateTimeInterface $periodStart
     * @param Id $envelopeId
     * @return Transaction[]
     */
    public function getTransactionsForEnvelope(Id $userId, Id $budgetId, DateTimeInterface $periodStart, Id $envelopeId): array;
}
