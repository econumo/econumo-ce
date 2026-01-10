<?php

declare(strict_types=1);

namespace App\EconumoBundle\Domain\Factory;

use App\EconumoBundle\Domain\Entity\ValueObject\DecimalNumber;
use DateTimeInterface;
use App\EconumoBundle\Domain\Entity\Transaction;
use App\EconumoBundle\Domain\Entity\ValueObject\Id;
use App\EconumoBundle\Domain\Entity\ValueObject\TransactionType;
use App\EconumoBundle\Domain\Exception\RecipientIsRequiredException;
use App\EconumoBundle\Domain\Repository\AccountRepositoryInterface;
use App\EconumoBundle\Domain\Repository\CategoryRepositoryInterface;
use App\EconumoBundle\Domain\Repository\PayeeRepositoryInterface;
use App\EconumoBundle\Domain\Repository\TagRepositoryInterface;
use App\EconumoBundle\Domain\Factory\TransactionFactoryInterface;
use App\EconumoBundle\Domain\Repository\TransactionRepositoryInterface;
use App\EconumoBundle\Domain\Repository\UserRepositoryInterface;
use App\EconumoBundle\Domain\Service\DatetimeServiceInterface;
use App\EconumoBundle\Domain\Service\Dto\TransactionDto;

class TransactionFactory implements TransactionFactoryInterface
{
    public function __construct(private readonly DatetimeServiceInterface $datetimeService, private readonly TransactionRepositoryInterface $transactionRepository, private readonly AccountRepositoryInterface $accountRepository, private readonly UserRepositoryInterface $userRepository, private readonly CategoryRepositoryInterface $categoryRepository, private readonly PayeeRepositoryInterface $payeeRepository, private readonly TagRepositoryInterface $tagRepository)
    {
    }

    public function create(TransactionDto $dto): Transaction
    {
        if ($dto->type->isTransfer() && !$dto->accountRecipientId instanceof Id) {
            throw new RecipientIsRequiredException('Recipient account is required for transfer transaction');
        }

        return new Transaction(
            $this->transactionRepository->getNextIdentity(),
            $this->userRepository->getReference($dto->userId),
            $dto->type,
            $this->accountRepository->getReference($dto->accountId),
            ($dto->categoryId instanceof Id ? $this->categoryRepository->getReference($dto->categoryId) : null),
            $dto->amount,
            $dto->date,
            $this->datetimeService->getCurrentDatetime(),
            ($dto->accountRecipientId instanceof Id ? $this->accountRepository->getReference($dto->accountRecipientId) : null),
            $dto->amountRecipient,
            $dto->description,
            ($dto->payeeId instanceof Id ? $this->payeeRepository->getReference($dto->payeeId) : null),
            ($dto->tagId instanceof Id ? $this->tagRepository->getReference($dto->tagId) :  null),
        );
    }

    public function createTransaction(
        Id $accountId,
        DecimalNumber $transaction,
        DateTimeInterface $transactionDate,
        string $comment = ''
    ): Transaction {
        $account = $this->accountRepository->get($accountId);
        return new Transaction(
            $this->transactionRepository->getNextIdentity(),
            $this->userRepository->getReference($account->getUserId()),
            new TransactionType($transaction->isLessThan(0) ? TransactionType::EXPENSE : TransactionType::INCOME),
            $this->accountRepository->getReference($accountId),
            null,
            $transaction->abs(),
            $transactionDate,
            $this->datetimeService->getCurrentDatetime(),
            null,
            null,
            $comment,
            null,
            null
        );
    }

    public function createCorrection(
        Id $accountId,
        DecimalNumber $correction,
        DateTimeInterface $transactionDate,
        string $comment = ''
    ): Transaction {
        $account = $this->accountRepository->get($accountId);
        return new Transaction(
            $this->transactionRepository->getNextIdentity(),
            $this->userRepository->getReference($account->getUserId()),
            new TransactionType($correction->isLessThan(0) ? TransactionType::INCOME : TransactionType::EXPENSE),
            $this->accountRepository->getReference($accountId),
            null,
            $correction->abs(),
            $transactionDate,
            $this->datetimeService->getCurrentDatetime(),
            null,
            null,
            $comment,
            null,
            null
        );
    }
}
