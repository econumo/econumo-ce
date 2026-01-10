<?php

declare(strict_types=1);

namespace App\EconumoBundle\Application\Account\Assembler;

use App\EconumoBundle\Application\Account\Assembler\AccountToDtoV1ResultAssembler;
use App\EconumoBundle\Application\Account\Dto\GetAccountListV1RequestDto;
use App\EconumoBundle\Application\Account\Dto\GetAccountListV1ResultDto;
use App\EconumoBundle\Domain\Entity\Account;
use App\EconumoBundle\Domain\Entity\ValueObject\Id;
use App\EconumoBundle\Domain\Entity\ValueObject\DecimalNumber;

class GetAccountListV1ResultAssembler
{
    public function __construct(private readonly AccountToDtoV1ResultAssembler $accountToDtoV1ResultAssembler)
    {
    }

    /**
     * @param Account[] $accounts
     */
    public function assemble(
        GetAccountListV1RequestDto $dto,
        Id $userId,
        array $accounts,
        array $balances
    ): GetAccountListV1ResultDto {
        $result = new GetAccountListV1ResultDto();
        $result->items = [];
        foreach (array_reverse($accounts) as $account) {
            $result->items[] = $this->accountToDtoV1ResultAssembler->assemble($userId, $account, $balances[$account->getId()->getValue()] ?? new DecimalNumber());
        }

        return $result;
    }
}
