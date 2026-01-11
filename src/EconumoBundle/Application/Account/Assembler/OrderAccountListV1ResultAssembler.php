<?php

declare(strict_types=1);

namespace App\EconumoBundle\Application\Account\Assembler;

use App\EconumoBundle\Application\Account\Dto\OrderAccountListV1RequestDto;
use App\EconumoBundle\Application\Account\Dto\OrderAccountListV1ResultDto;
use App\EconumoBundle\Domain\Entity\Account;
use App\EconumoBundle\Domain\Entity\ValueObject\Id;
use App\EconumoBundle\Domain\Entity\ValueObject\DecimalNumber;
use App\EconumoBundle\Domain\Repository\AccountRepositoryInterface;
use App\EconumoBundle\Domain\Service\AccountServiceInterface;

readonly class OrderAccountListV1ResultAssembler
{
    public function __construct(
        private AccountRepositoryInterface $accountRepository,
        private AccountToDtoV1ResultAssembler $accountToDtoV1ResultAssembler,
        private AccountServiceInterface $accountService
    ) {
    }

    public function assemble(
        OrderAccountListV1RequestDto $dto,
        Id $userId
    ): OrderAccountListV1ResultDto {
        $result = new OrderAccountListV1ResultDto();
        $result->items = [];
        $accounts = $this->accountRepository->getAvailableForUserId($userId);
        $accountsIds = array_map(static fn(Account $account): Id => $account->getId(), $accounts);
        $balances = $this->accountService->getAccountsBalance($accountsIds);
        foreach ($accounts as $account) {
            $result->items[] = $this->accountToDtoV1ResultAssembler->assemble(
                $userId,
                $account,
                $balances[$account->getId()->getValue()] ?? new DecimalNumber()
            );
        }

        return $result;
    }
}
