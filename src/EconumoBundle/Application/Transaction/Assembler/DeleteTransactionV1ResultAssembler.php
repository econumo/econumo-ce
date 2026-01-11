<?php

declare(strict_types=1);

namespace App\EconumoBundle\Application\Transaction\Assembler;

use App\EconumoBundle\Application\Account\Assembler\AccountToDtoV1ResultAssembler;
use App\EconumoBundle\Application\Transaction\Dto\DeleteTransactionV1RequestDto;
use App\EconumoBundle\Application\Transaction\Dto\DeleteTransactionV1ResultDto;
use App\EconumoBundle\Domain\Entity\Account;
use App\EconumoBundle\Domain\Entity\Transaction;
use App\EconumoBundle\Domain\Entity\ValueObject\Id;
use App\EconumoBundle\Domain\Entity\ValueObject\DecimalNumber;
use App\EconumoBundle\Domain\Repository\AccountRepositoryInterface;
use App\EconumoBundle\Domain\Service\AccountServiceInterface;

readonly class DeleteTransactionV1ResultAssembler
{
    public function __construct(
        private AccountRepositoryInterface $accountRepository,
        private TransactionToDtoResultAssembler $transactionToDtoV1ResultAssembler,
        private AccountToDtoV1ResultAssembler $accountToDtoV1ResultAssembler,
        private AccountServiceInterface $accountService
    ) {
    }

    public function assemble(
        DeleteTransactionV1RequestDto $dto,
        Id $userId,
        Transaction $transaction
    ): DeleteTransactionV1ResultDto {
        $result = new DeleteTransactionV1ResultDto();
        $result->item = $this->transactionToDtoV1ResultAssembler->assemble($transaction);
        $accounts = $this->accountRepository->getAvailableForUserId($userId);
        $accountsIds = array_map(static fn(Account $account): Id => $account->getId(), $accounts);
        $balances = $this->accountService->getAccountsBalance($accountsIds);
        foreach (array_reverse($accounts) as $account) {
            $result->accounts[] = $this->accountToDtoV1ResultAssembler->assemble($userId, $account, $balances[$account->getId()->getValue()] ?? new DecimalNumber());
        }

        return $result;
    }
}
