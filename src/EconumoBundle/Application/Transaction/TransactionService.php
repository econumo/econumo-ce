<?php

declare(strict_types=1);

namespace App\EconumoBundle\Application\Transaction;

use App\EconumoBundle\Application\Transaction\Assembler\UpdateTransactionRequestToDomainDtoAssembler;
use App\EconumoBundle\Application\Transaction\Dto\UpdateTransactionV1RequestDto;
use App\EconumoBundle\Application\Transaction\Dto\UpdateTransactionV1ResultDto;
use App\EconumoBundle\Application\Transaction\Assembler\UpdateTransactionV1ResultAssembler;
use App\EconumoBundle\Application\Exception\ValidationException;
use App\EconumoBundle\Application\Transaction\Assembler\RequestToDomainDtoAssembler;
use App\EconumoBundle\Application\Transaction\Dto\CreateTransactionV1RequestDto;
use App\EconumoBundle\Application\Transaction\Dto\CreateTransactionV1ResultDto;
use App\EconumoBundle\Application\Transaction\Assembler\CreateTransactionV1ResultAssembler;
use App\EconumoBundle\Application\Transaction\Dto\DeleteTransactionV1RequestDto;
use App\EconumoBundle\Application\Transaction\Dto\DeleteTransactionV1ResultDto;
use App\EconumoBundle\Application\Transaction\Assembler\DeleteTransactionV1ResultAssembler;
use App\EconumoBundle\Domain\Entity\ValueObject\Id;
use App\EconumoBundle\Domain\Repository\TransactionRepositoryInterface;
use App\EconumoBundle\Domain\Service\AccountAccessServiceInterface;
use App\EconumoBundle\Domain\Service\TransactionServiceInterface;
use App\EconumoBundle\Domain\Service\Translation\TranslationServiceInterface;

class TransactionService
{
    public function __construct(private readonly CreateTransactionV1ResultAssembler $createTransactionV1ResultAssembler, private readonly RequestToDomainDtoAssembler $requestToDomainDtoAssembler, private readonly TransactionServiceInterface $transactionService, private readonly DeleteTransactionV1ResultAssembler $deleteTransactionV1ResultAssembler, private readonly TransactionRepositoryInterface $transactionRepository, private readonly AccountAccessServiceInterface $accountAccessService, private readonly UpdateTransactionV1ResultAssembler $updateTransactionV1ResultAssembler, private readonly UpdateTransactionRequestToDomainDtoAssembler $updateTransactionRequestToDomainDtoAssembler, private readonly TranslationServiceInterface $translationService)
    {
    }

    public function createTransaction(
        CreateTransactionV1RequestDto $dto,
        Id $userId
    ): CreateTransactionV1ResultDto {
        $accountId = new Id($dto->accountId);
        if (!$this->accountAccessService->canAddTransaction($userId, $accountId)) {
            throw new ValidationException($this->translationService->trans('account.account.not_available', ['id' => $dto->accountId]));
        }

        $transactionDto = $this->requestToDomainDtoAssembler->assemble($dto, $userId);
        $transaction = $this->transactionService->createTransaction($transactionDto);

        return $this->createTransactionV1ResultAssembler->assemble($dto, $userId, $transaction);
    }

    public function deleteTransaction(
        DeleteTransactionV1RequestDto $dto,
        Id $userId
    ): DeleteTransactionV1ResultDto {
        $transaction = $this->transactionRepository->get(new Id($dto->id));
        if (!$this->accountAccessService->canDeleteTransaction($userId, $transaction->getAccountId())) {
            throw new ValidationException($this->translationService->trans('transaction.transaction.not_available', ['id' => $dto->id]));
        }

        $this->transactionService->deleteTransaction($transaction);
        return $this->deleteTransactionV1ResultAssembler->assemble($dto, $userId, $transaction);
    }

    public function updateTransaction(
        UpdateTransactionV1RequestDto $dto,
        Id $userId
    ): UpdateTransactionV1ResultDto {
        $accountId = new Id($dto->accountId);
        if (!$this->accountAccessService->canUpdateTransaction($userId, $accountId)) {
            throw new ValidationException($this->translationService->trans('account.account.not_available', ['id' => $dto->accountId]));
        }

        $transactionDto = $this->updateTransactionRequestToDomainDtoAssembler->assemble($dto, $userId);
        $transaction = $this->transactionService->updateTransaction(new Id($dto->id), $transactionDto);

        return $this->updateTransactionV1ResultAssembler->assemble($dto, $userId, $transaction);
    }
}
