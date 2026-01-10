<?php

declare(strict_types=1);

namespace App\EconumoBundle\Application\Connection;

use App\EconumoBundle\Application\Connection\Assembler\SetAccountAccessV1ResultAssembler;
use App\EconumoBundle\Application\Connection\Dto\RevokeAccountAccessV1RequestDto;
use App\EconumoBundle\Application\Connection\Dto\RevokeAccountAccessV1ResultDto;
use App\EconumoBundle\Application\Connection\Assembler\RevokeAccountAccessV1ResultAssembler;
use App\EconumoBundle\Application\Connection\Dto\SetAccountAccessV1RequestDto;
use App\EconumoBundle\Application\Connection\Dto\SetAccountAccessV1ResultDto;
use App\EconumoBundle\Application\Exception\AccessDeniedException;
use App\EconumoBundle\Domain\Entity\ValueObject\AccountUserRole;
use App\EconumoBundle\Domain\Entity\ValueObject\Id;
use App\EconumoBundle\Domain\Service\AccountAccessServiceInterface;
use App\EconumoBundle\Domain\Service\Connection\ConnectionAccountServiceInterface;

class AccountAccessService
{
    public function __construct(private readonly SetAccountAccessV1ResultAssembler $setAccountAccessV1ResultAssembler, private readonly ConnectionAccountServiceInterface $connectionAccountService, private readonly AccountAccessServiceInterface $accountAccessService, private readonly RevokeAccountAccessV1ResultAssembler $revokeAccountAccessV1ResultAssembler)
    {
    }

    public function setAccountAccess(
        SetAccountAccessV1RequestDto $dto,
        Id $userId
    ): SetAccountAccessV1ResultDto {
        $accountId = new Id($dto->accountId);
        if (!$this->accountAccessService->canUpdateAccount($userId, $accountId)) {
            throw new AccessDeniedException();
        }

        $affectedUserId = new Id($dto->userId);
        $role = AccountUserRole::createFromAlias($dto->role);
        $this->connectionAccountService->setAccountAccess($affectedUserId, $accountId, $role);
        return $this->setAccountAccessV1ResultAssembler->assemble($dto);
    }

    public function revokeAccountAccess(
        RevokeAccountAccessV1RequestDto $dto,
        Id $userId
    ): RevokeAccountAccessV1ResultDto {
        $accountId = new Id($dto->accountId);
        if (!$this->accountAccessService->canUpdateAccount($userId, $accountId)) {
            throw new AccessDeniedException();
        }

        $affectedUserId = new Id($dto->userId);
        $this->connectionAccountService->revokeAccountAccess($affectedUserId, $accountId);
        return $this->revokeAccountAccessV1ResultAssembler->assemble($dto);
    }
}
