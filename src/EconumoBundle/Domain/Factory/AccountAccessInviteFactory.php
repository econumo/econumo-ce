<?php

declare(strict_types=1);


namespace App\EconumoBundle\Domain\Factory;


use App\EconumoBundle\Domain\Entity\AccountAccessInvite;
use App\EconumoBundle\Domain\Entity\ValueObject\AccountUserRole;
use App\EconumoBundle\Domain\Entity\ValueObject\Id;
use App\EconumoBundle\Domain\Factory\AccountAccessInviteFactoryInterface;
use App\EconumoBundle\Domain\Repository\AccountRepositoryInterface;
use App\EconumoBundle\Domain\Repository\UserRepositoryInterface;
use App\EconumoBundle\Domain\Service\DatetimeServiceInterface;

class AccountAccessInviteFactory implements AccountAccessInviteFactoryInterface
{
    public function __construct(private readonly DatetimeServiceInterface $datetimeService, private readonly AccountRepositoryInterface $accountRepository, private readonly UserRepositoryInterface $userRepository)
    {
    }

    public function create(Id $accountId, Id $recipientId, Id $ownerId, AccountUserRole $role): AccountAccessInvite
    {
        return new AccountAccessInvite(
            $this->accountRepository->getReference($accountId),
            $this->userRepository->getReference($recipientId),
            $this->userRepository->getReference($ownerId),
            $role,
            str_pad((string)random_int(0, 99999), 5, '0', STR_PAD_LEFT),
            $this->datetimeService->getCurrentDatetime()
        );
    }
}
