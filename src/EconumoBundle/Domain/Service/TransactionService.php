<?php

declare(strict_types=1);

namespace App\EconumoBundle\Domain\Service;

use App\EconumoBundle\Domain\Entity\ValueObject\DecimalNumber;
use Throwable;
use App\EconumoBundle\Domain\Entity\Account;
use App\EconumoBundle\Domain\Entity\Transaction;
use App\EconumoBundle\Domain\Entity\ValueObject\Id;
use App\EconumoBundle\Domain\Factory\TransactionFactoryInterface;
use App\EconumoBundle\Domain\Repository\AccountRepositoryInterface;
use App\EconumoBundle\Domain\Repository\FolderRepositoryInterface;
use App\EconumoBundle\Domain\Repository\TransactionRepositoryInterface;
use App\EconumoBundle\Domain\Service\Dto\TransactionDto;
use DateTimeInterface;

readonly class TransactionService implements TransactionServiceInterface
{
    public function __construct(
        private TransactionRepositoryInterface $transactionRepository,
        private TransactionFactoryInterface $transactionFactory,
        private AccountRepositoryInterface $accountRepository,
        private AntiCorruptionServiceInterface $antiCorruptionService,
        private FolderRepositoryInterface $folderRepository
    ) {
    }

    public function createTransaction(TransactionDto $transactionDto): Transaction
    {
        $this->antiCorruptionService->beginTransaction(__METHOD__);
        try {
            $transaction = $this->transactionFactory->create($transactionDto);
            $this->transactionRepository->save([$transaction]);
            $this->antiCorruptionService->commit(__METHOD__);
        } catch (Throwable $throwable) {
            $this->antiCorruptionService->rollback(__METHOD__);
            throw $throwable;
        }

        return $transaction;
    }

    public function updateTransaction(Id $id, TransactionDto $transactionDto): Transaction
    {
        $this->antiCorruptionService->beginTransaction(__METHOD__);
        $transaction = $this->transactionRepository->get($id);
        try {
            $transaction->update($transactionDto);
            $this->transactionRepository->save([$transaction]);
            $this->antiCorruptionService->commit(__METHOD__);
        } catch (Throwable $throwable) {
            $this->antiCorruptionService->rollback(__METHOD__);
            throw $throwable;
        }

        return $transaction;
    }

    public function deleteTransaction(Transaction $transaction): void
    {
        $this->antiCorruptionService->beginTransaction(__METHOD__);
        try {
            $this->transactionRepository->delete($transaction);
            $this->antiCorruptionService->commit(__METHOD__);
        } catch (Throwable $throwable) {
            $this->antiCorruptionService->rollback(__METHOD__);
            throw $throwable;
        }
    }

    public function updateBalance(
        Id $accountId,
        DecimalNumber $correction,
        DateTimeInterface $updatedAt,
        string $comment = ''
    ): Transaction {
        $this->antiCorruptionService->beginTransaction(__METHOD__);
        try {
            $transaction = $this->transactionFactory->createCorrection($accountId, $correction, $updatedAt, $comment);
            $this->transactionRepository->save([$transaction]);
            $this->antiCorruptionService->commit(__METHOD__);
        } catch (Throwable $throwable) {
            $this->antiCorruptionService->rollback(__METHOD__);
            throw $throwable;
        }

        return $transaction;
    }

    public function getChanged(Id $userId, DateTimeInterface $lastUpdate): array
    {
        return $this->transactionRepository->findChanges($userId, $lastUpdate);
    }

    /**
     * @inheritDoc
     */
    public function getTransactionsForVisibleAccounts(
        Id $userId,
        DateTimeInterface $periodStart = null,
        DateTimeInterface $periodEnd = null
    ): array {
        $folders = $this->folderRepository->getByUserId($userId);
        $accounts = $this->accountRepository->getUserAccounts($userId);
        $excludeAccountIds = [];
        foreach ($accounts as $account) {
            if ($account->isDeleted()) {
                $excludeAccountIds[] = $account->getId();
                continue;
            }

            foreach ($folders as $folder) {
                if ($folder->containsAccount($account) && !$folder->isVisible()) {
                    $excludeAccountIds[] = $account->getId();
                }
            }
        }

        return $this->transactionRepository->findAvailableForUserId(
            $userId,
            $excludeAccountIds,
            $periodStart,
            $periodEnd
        );
    }

    /**
     * @param array<int, Id>|null $accountIds
     */
    public function exportTransactionList(Id $userId, ?array $accountIds = null): array
    {
        $accounts = $this->accountRepository->getUserAccounts($userId);
        $accountsById = [];
        foreach ($accounts as $account) {
            $accountsById[$account->getId()->getValue()] = $account;
        }
        $allAccountsById = $accountsById;

        if ($accountIds !== null) {
            $accountIdLookup = [];
            foreach ($accountIds as $accountId) {
                $accountIdLookup[$accountId->getValue()] = true;
            }

            $accountsById = array_intersect_key($accountsById, $accountIdLookup);
        }

        $rows = [$this->getExportHeaders()];
        if ($accountsById === []) {
            return $rows;
        }

        $transactions = $this->transactionRepository->findAvailableForUserId($userId);
        foreach ($transactions as $transaction) {
            foreach ($this->buildExportRows($transaction, $accountsById, $allAccountsById) as $row) {
                $rows[] = $row;
            }
        }

        return $rows;
    }

    /**
     * @return array<int, string>
     */
    private function getExportHeaders(): array
    {
        return [
            'transaction_id',
            'account_name',
            'account_currency',
            'category',
            'description',
            'tag',
            'payee',
            'amount',
            'date',
        ];
    }

    /**
     * @param array<string, Account> $accountsById
     * @return array<int, array<int, string>>
     */
    private function buildExportRows(Transaction $transaction, array $accountsById, array $allAccountsById): array
    {
        $rows = [];
        $accountId = $transaction->getAccountId()->getValue();
        if (isset($accountsById[$accountId])) {
            $description = $transaction->getDescription();
            if ($transaction->getType()->isTransfer()) {
                $recipientId = $transaction->getAccountRecipientId()?->getValue();
                $transferNote = 'Transfer';
                if ($recipientId && isset($allAccountsById[$recipientId])) {
                    $transferNote = sprintf(
                        'Transfer of %s %s to %s',
                        $this->formatAmountForDescription($transaction->getAmount()),
                        $accountsById[$accountId]->getCurrencyCode()->getValue(),
                        $allAccountsById[$recipientId]->getName()->getValue()
                    );
                }

                if (trim($description) === '') {
                    $description = $transferNote;
                } else {
                    $description = $transferNote . ' [' . trim($description) . ']';
                }
            }

            $rows[] = $this->buildExportRow(
                $transaction,
                $accountsById[$accountId],
                $this->formatAmount(
                    $transaction->getAmount(),
                    $transaction->getType()->isExpense() || $transaction->getType()->isTransfer()
                ),
                $transaction->getCategory()?->getName()->getValue(),
                $transaction->getTag()?->getName()->getValue(),
                $transaction->getPayee()?->getName()->getValue(),
                $description
            );
        }

        if ($transaction->getType()->isTransfer()) {
            $recipientId = $transaction->getAccountRecipientId()?->getValue();
            if ($recipientId && isset($accountsById[$recipientId])) {
                $sourceName = isset($allAccountsById[$accountId])
                    ? $allAccountsById[$accountId]->getName()->getValue()
                    : '';
                $transferNote = $sourceName !== ''
                    ? sprintf(
                        'Transfer of %s %s from %s',
                        $this->formatAmountForDescription($transaction->getAmountRecipient() ?? $transaction->getAmount()),
                        $accountsById[$recipientId]->getCurrencyCode()->getValue(),
                        $sourceName
                    )
                    : 'Transfer';
                $description = $transaction->getDescription();
                if (trim($description) === '') {
                    $description = $transferNote;
                } else {
                    $description = $transferNote . ' [' . trim($description) . ']';
                }
                $rows[] = $this->buildExportRow(
                    $transaction,
                    $accountsById[$recipientId],
                    $this->formatAmount(
                        $transaction->getAmountRecipient() ?? $transaction->getAmount(),
                        false
                    ),
                    '',
                    '',
                    '',
                    $description
                );
            }
        }

        return $rows;
    }

    private function buildExportRow(
        Transaction $transaction,
        Account $account,
        string $amount,
        ?string $category,
        ?string $tag,
        ?string $payee,
        ?string $description = null
    ): array {
        return [
            $transaction->getId()->getValue(),
            $this->sanitizeExportValue($account->getName()->getValue()),
            $account->getCurrencyCode()->getValue(),
            $this->sanitizeExportValue($category),
            $this->sanitizeExportValue($description ?? $transaction->getDescription()),
            $this->sanitizeExportValue($tag),
            $this->sanitizeExportValue($payee),
            $amount,
            $transaction->getSpentAt()->format('Y-m-d H:i:s'),
        ];
    }

    private function formatAmount(DecimalNumber $amount, bool $negative): string
    {
        $value = $amount->getValue();
        if ($negative) {
            return str_starts_with($value, '-') ? $value : '-' . $value;
        }

        return str_starts_with($value, '-') ? substr($value, 1) : $value;
    }

    private function formatAmountForDescription(DecimalNumber $amount): string
    {
        $value = $amount->getValue();
        return str_starts_with($value, '-') ? substr($value, 1) : $value;
    }

    private function sanitizeExportValue(?string $value): string
    {
        if ($value === null) {
            return '';
        }

        $value = preg_replace("/[\\r\\n]+/", ' ', $value);
        if ($value === null) {
            return '';
        }

        return trim($value);
    }
}
