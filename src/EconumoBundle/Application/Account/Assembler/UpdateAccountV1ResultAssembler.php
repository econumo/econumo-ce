<?php

declare(strict_types=1);

namespace App\EconumoBundle\Application\Account\Assembler;

use App\EconumoBundle\Application\Account\Dto\UpdateAccountV1RequestDto;
use App\EconumoBundle\Application\Account\Dto\UpdateAccountV1ResultDto;
use App\EconumoBundle\Application\Transaction\Assembler\TransactionToDtoResultAssembler;
use App\EconumoBundle\Domain\Entity\Account;
use App\EconumoBundle\Domain\Entity\Transaction;
use App\EconumoBundle\Domain\Entity\ValueObject\Id;
use App\EconumoBundle\Domain\Service\AccountServiceInterface;

readonly class UpdateAccountV1ResultAssembler
{
    public function __construct(
        private AccountToDtoV1ResultAssembler $accountToDtoV1ResultAssembler,
        private TransactionToDtoResultAssembler $transactionToDtoV1ResultAssembler,
        private AccountServiceInterface $accountService
    ) {
    }

    public function assemble(
        UpdateAccountV1RequestDto $dto,
        Id $userId,
        Account $account,
        ?Transaction $transaction = null
    ): UpdateAccountV1ResultDto {
        $result = new UpdateAccountV1ResultDto();
        $balance = $this->accountService->getBalance($account->getId());
        $result->item = $this->accountToDtoV1ResultAssembler->assemble($userId, $account, $balance);
        if ($transaction instanceof Transaction) {
            $result->transaction = $this->transactionToDtoV1ResultAssembler->assemble($transaction);
        }

        return $result;
    }
}
