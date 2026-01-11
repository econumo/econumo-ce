<?php

declare(strict_types=1);

namespace App\EconumoBundle\Application\Account\Assembler;

use App\EconumoBundle\Application\Account\Dto\CreateAccountV1RequestDto;
use App\EconumoBundle\Application\Account\Dto\CreateAccountV1ResultDto;
use App\EconumoBundle\Domain\Entity\Account;
use App\EconumoBundle\Domain\Entity\ValueObject\Id;
use App\EconumoBundle\Domain\Entity\ValueObject\DecimalNumber;

readonly class CreateAccountV1ResultAssembler
{
    public function __construct(private AccountToDtoV1ResultAssembler $accountToDtoV1ResultAssembler)
    {
    }

    public function assemble(
        CreateAccountV1RequestDto $dto,
        Id $userId,
        Account $account,
        DecimalNumber $balance
    ): CreateAccountV1ResultDto {
        $result = new CreateAccountV1ResultDto();
        $result->item = $this->accountToDtoV1ResultAssembler->assemble($userId, $account, $balance);

        return $result;
    }
}
