<?php

declare(strict_types=1);


namespace App\EconumoBundle\Domain\Service\Connection;


use App\EconumoBundle\Domain\Entity\AccountAccess;
use App\EconumoBundle\Domain\Entity\ValueObject\AccountUserRole;
use App\EconumoBundle\Domain\Entity\ValueObject\Id;

interface ConnectionAccountServiceInterface
{
    /**
     * @param Id $userId
     * @return AccountAccess[]
     */
    public function getReceivedAccountAccess(Id $userId): array;

    /**
     * @param Id $userId
     * @return AccountAccess[]
     */
    public function getIssuedAccountAccess(Id $userId): array;

    public function revokeAccountAccess(Id $userId, Id $sharedAccountId): void;

    public function setAccountAccess(Id $userId, Id $sharedAccountId, AccountUserRole $role): void;
}
