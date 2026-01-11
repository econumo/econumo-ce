<?php

declare(strict_types=1);

namespace App\EconumoBundle\Application\Transaction\Assembler;

use App\EconumoBundle\Application\Transaction\Dto\CreateTransactionV1RequestDto;
use App\EconumoBundle\Domain\Entity\ValueObject\Id;
use App\EconumoBundle\Domain\Entity\ValueObject\DecimalNumber;
use App\EconumoBundle\Domain\Entity\ValueObject\TransactionType;
use App\EconumoBundle\Domain\Repository\AccountRepositoryInterface;
use App\EconumoBundle\Domain\Repository\CategoryRepositoryInterface;
use App\EconumoBundle\Domain\Repository\PayeeRepositoryInterface;
use App\EconumoBundle\Domain\Repository\TagRepositoryInterface;
use App\EconumoBundle\Domain\Service\Dto\TransactionDto;
use DateTime;

class RequestToDomainDtoAssembler
{
    public function __construct(private readonly AccountRepositoryInterface $accountRepository, private readonly CategoryRepositoryInterface $categoryRepository, private readonly TagRepositoryInterface $tagRepository, private readonly PayeeRepositoryInterface $payeeRepository)
    {
    }

    public function assemble(
        CreateTransactionV1RequestDto $dto,
        Id $userId
    ): TransactionDto {
        $result = new TransactionDto();
        $result->type = TransactionType::createFromAlias($dto->type);
        $result->userId = $userId;
        $result->amount = new DecimalNumber($dto->amount);
        $result->accountId = new Id($dto->accountId);
        $result->account = $this->accountRepository->getReference($result->accountId);
        $result->accountRecipientId = null;
        $result->accountRecipient = null;
        $result->amountRecipient = $dto->amountRecipient === null ? null : new DecimalNumber($dto->amountRecipient);
        $result->description = $dto->description ?? '';
        $result->date = DateTime::createFromFormat('Y-m-d H:i:s', $dto->date);
        $result->categoryId = null;
        $result->category = null;
        $result->payeeId = null;
        $result->payee = null;
        $result->tagId = null;
        $result->tag = null;

        if ($result->type->isTransfer()) {
            if ($dto->amountRecipient !== null) {
                $result->amountRecipient = new DecimalNumber($dto->amountRecipient);
            }

            if ($dto->accountRecipientId !== null) {
                $result->accountRecipientId = new Id($dto->accountRecipientId);
                $result->accountRecipient = $this->accountRepository->getReference($result->accountRecipientId);
            }
        } else {
            $result->categoryId = new Id($dto->categoryId);
            $result->category = $this->categoryRepository->getReference($result->categoryId);
            if ($dto->payeeId !== null) {
                $result->payeeId = new Id($dto->payeeId);
                $result->payee = $this->payeeRepository->getReference($result->payeeId);
            }

            if ($dto->tagId !== null) {
                $result->tagId = new Id($dto->tagId);
                $result->tag = $this->tagRepository->getReference($result->tagId);
            }
        }

        return $result;
    }
}
