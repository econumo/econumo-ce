<?php

declare(strict_types=1);

namespace App\EconumoBundle\Application\Transaction\Assembler;

use App\EconumoBundle\Application\Transaction\Assembler\TransactionToDtoResultAssembler;
use App\EconumoBundle\Application\Transaction\Dto\GetTransactionListV1RequestDto;
use App\EconumoBundle\Application\Transaction\Dto\GetTransactionListV1ResultDto;
use App\EconumoBundle\Domain\Entity\Transaction;
use App\EconumoBundle\Domain\Entity\ValueObject\Id;

class GetTransactionListV1ResultAssembler
{
    public function __construct(private readonly TransactionToDtoResultAssembler $transactionToDtoV1ResultAssembler)
    {
    }

    /**
     * @param Transaction[] $transactions
     */
    public function assemble(
        GetTransactionListV1RequestDto $dto,
        Id $userId,
        array $transactions
    ): GetTransactionListV1ResultDto {
        $result = new GetTransactionListV1ResultDto();
        $result->items = [];
        foreach ($transactions as $transaction) {
            $result->items[] = $this->transactionToDtoV1ResultAssembler->assemble($transaction);
        }

        return $result;
    }
}
