<?php

declare(strict_types=1);

namespace App\EconumoBundle\Application\Transaction\Assembler;

use App\EconumoBundle\Domain\Entity\ValueObject\DecimalNumber;
use App\EconumoBundle\Domain\Entity\ValueObject\Id;
use App\EconumoBundle\Application\Transaction\Dto\TransactionResultDto;
use App\EconumoBundle\Application\User\Assembler\UserToDtoResultAssembler;
use App\EconumoBundle\Domain\Entity\Transaction;

readonly class TransactionToDtoResultAssembler
{
    public function __construct(
        private UserToDtoResultAssembler $userToDtoResultAssembler
    )
    {
    }

    public function assemble(
        Transaction $transaction
    ): TransactionResultDto {
        $item = new TransactionResultDto();
        $item->id = $transaction->getId()->getValue();
        $item->author = $this->userToDtoResultAssembler->assemble($transaction->getUser());
        $item->type = $transaction->getType()->getAlias();
        $item->accountId = $transaction->getAccountId()->getValue();
        $item->accountRecipientId = $transaction->getAccountRecipientId(
        ) instanceof Id ? $transaction->getAccountRecipientId()->getValue() : null;
        $item->amount = $transaction->getAmount()->getValue();
        $item->amountRecipient = $transaction->getAmountRecipient() instanceof DecimalNumber ? $transaction->getAmountRecipient()->getValue() : $transaction->getAmount()->getValue();
        $item->categoryId = $transaction->getCategoryId() instanceof Id ? $transaction->getCategoryId()->getValue() : null;
        $item->description = $transaction->getDescription();
        $item->payeeId = $transaction->getPayeeId() instanceof Id ? $transaction->getPayeeId()->getValue() : null;
        $item->tagId = $transaction->getTagId() instanceof Id ? $transaction->getTagId()->getValue() : null;
        $item->date = $transaction->getSpentAt()->format('Y-m-d H:i:s');

        return $item;
    }
}
