<?php

declare(strict_types=1);


namespace App\EconumoBundle\Application\Account\Assembler;


use App\EconumoBundle\Application\Account\Dto\SharedAccessItemResultDto;
use App\EconumoBundle\Application\User\Assembler\UserIdToDtoResultAssembler;
use App\EconumoBundle\Domain\Entity\ValueObject\Id;
use App\EconumoBundle\Domain\Repository\AccountAccessRepositoryInterface;

class AccountIdToSharedAccessResultAssembler
{
    public function __construct(private readonly AccountAccessRepositoryInterface $accountAccessRepository, private readonly UserIdToDtoResultAssembler $userIdToDtoResultAssembler)
    {
    }

    /**
     * @return SharedAccessItemResultDto[]
     */
    public function assemble(Id $accountId): array
    {
        $result = [];
        $accessList = $this->accountAccessRepository->getByAccount($accountId);
        foreach ($accessList as $access) {
            $sharedAccess = new SharedAccessItemResultDto();
            $sharedAccess->user = $this->userIdToDtoResultAssembler->assemble($access->getUserId());
            $sharedAccess->role = $access->getRole()->getAlias();
            $result[] = $sharedAccess;
        }

        return $result;
    }
}
