<?php

declare(strict_types=1);

namespace App\EconumoBundle\Domain\Service;

use App\EconumoBundle\Domain\Entity\Transaction;
use App\EconumoBundle\Domain\Entity\ValueObject\Id;
use App\EconumoBundle\Domain\Entity\ValueObject\DecimalNumber;
use App\EconumoBundle\Domain\Service\Dto\TransactionDto;
use DateTimeInterface;

interface TransactionServiceInterface
{
    public function createTransaction(TransactionDto $transactionDto): Transaction;

    public function updateTransaction(Id $id, TransactionDto $transactionDto): Transaction;

    public function deleteTransaction(Transaction $transaction): void;

    public function updateBalance(Id $accountId, DecimalNumber $correction, DateTimeInterface $updatedAt, string $comment = ''): Transaction;

    /**
     * @return Transaction[]
     */
    public function getTransactionsForVisibleAccounts(Id $userId, DateTimeInterface $periodStart = null, DateTimeInterface $periodEnd = null): array;

    /**
     * @param array<int, Id>|null $accountIds
     * @return array<int, array<int, string>>
     */
    public function exportTransactionList(Id $userId, ?array $accountIds = null): array;
}
