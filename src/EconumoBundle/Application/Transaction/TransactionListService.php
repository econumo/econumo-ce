<?php

declare(strict_types=1);

namespace App\EconumoBundle\Application\Transaction;

use App\EconumoBundle\Application\Transaction\Assembler\GetTransactionListV1ResultAssembler;
use App\EconumoBundle\Application\Transaction\Assembler\ImportTransactionListV1ResultAssembler;
use App\EconumoBundle\Application\Transaction\Dto\GetTransactionListV1RequestDto;
use App\EconumoBundle\Application\Transaction\Dto\GetTransactionListV1ResultDto;
use App\EconumoBundle\Application\Transaction\Dto\ImportTransactionListV1RequestDto;
use App\EconumoBundle\Application\Transaction\Dto\ImportTransactionListV1ResultDto;
use App\EconumoBundle\Domain\Entity\ValueObject\Id;
use App\EconumoBundle\Domain\Repository\TransactionRepositoryInterface;
use App\EconumoBundle\Domain\Service\AccountAccessServiceInterface;
use App\EconumoBundle\Domain\Service\ImportTransactionServiceInterface;
use App\EconumoBundle\Domain\Service\TransactionServiceInterface;
use DateTimeImmutable;
use App\EconumoBundle\Application\Transaction\Dto\ExportTransactionListV1RequestDto;

readonly class TransactionListService
{
    public function __construct(
        private GetTransactionListV1ResultAssembler $getTransactionListV1ResultAssembler,
        private ImportTransactionListV1ResultAssembler $importTransactionListV1ResultAssembler,
        private TransactionRepositoryInterface $transactionRepository,
        private AccountAccessServiceInterface $accountAccessService,
        private TransactionServiceInterface $transactionService,
        private ImportTransactionServiceInterface $importTransactionService,
    ) {
    }

    public function importTransactionList(
        ImportTransactionListV1RequestDto $dto,
        Id $userId
    ): ImportTransactionListV1ResultDto {
        if (!$dto->file) {
            $result = new ImportTransactionListV1ResultDto();
            $result->errors[] = 'No file provided';
            return $result;
        }

        $overrides = [];
        if ($dto->accountId !== null && trim($dto->accountId) !== '') {
            $accountId = trim($dto->accountId);
            if (!$this->accountAccessService->canAddTransaction($userId, new Id($accountId))) {
                $result = new ImportTransactionListV1ResultDto();
                $result->errors[] = 'Account access is not allowed';
                return $result;
            }

            $overrides['accountId'] = $accountId;
        }
        if ($dto->date !== null && trim($dto->date) !== '') {
            $overrides['date'] = trim($dto->date);
        }
        if ($dto->categoryId !== null && trim($dto->categoryId) !== '') {
            $overrides['categoryId'] = trim($dto->categoryId);
        }
        if ($dto->description !== null) {
            $overrides['description'] = trim($dto->description);
        }
        if ($dto->payeeId !== null && trim($dto->payeeId) !== '') {
            $overrides['payeeId'] = trim($dto->payeeId);
        }
        if ($dto->tagId !== null && trim($dto->tagId) !== '') {
            $overrides['tagId'] = trim($dto->tagId);
        }

        $domainResult = $this->importTransactionService->importFromCsv(
            $dto->file,
            $dto->mapping,
            $userId,
            $overrides
        );

        return $this->importTransactionListV1ResultAssembler->assemble($domainResult);
    }

    public function getTransactionList(
        GetTransactionListV1RequestDto $dto,
        Id $userId
    ): GetTransactionListV1ResultDto {
        if ($dto->accountId) {
            $this->accountAccessService->checkViewTransactionsAccess($userId, new Id($dto->accountId));
            $transactions = $this->transactionRepository->findByAccountId(new Id($dto->accountId));
        } elseif ($dto->periodStart && $dto->periodEnd) {
            $periodStart = new DateTimeImmutable($dto->periodStart);
            $periodEnd = new DateTimeImmutable($dto->periodEnd);
            $transactions = $this->transactionService->getTransactionsForVisibleAccounts(
                $userId,
                $periodStart,
                $periodEnd
            );
        } else {
            $transactions = $this->transactionService->getTransactionsForVisibleAccounts($userId);
        }

        return $this->getTransactionListV1ResultAssembler->assemble($dto, $userId, $transactions);
    }

    public function exportTransactionList(
        ExportTransactionListV1RequestDto $dto,
        Id $userId
    ): array {
        $accountIds = $this->parseAccountIds($dto->accountId);

        return $this->transactionService->exportTransactionList($userId, $accountIds);
    }

    /**
     * @return array<int, Id>|null
     */
    private function parseAccountIds(?string $accountId): ?array
    {
        if ($accountId === null) {
            return null;
        }

        $accountId = trim($accountId);
        if ($accountId === '') {
            return null;
        }

        $ids = array_filter(
            array_map('trim', explode(',', $accountId)),
            static fn(string $value): bool => $value !== ''
        );
        if ($ids === []) {
            return null;
        }

        $uniqueIds = array_values(array_unique($ids));
        return array_map(static fn(string $id): Id => new Id($id), $uniqueIds);
    }
}
