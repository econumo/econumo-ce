<?php

declare(strict_types=1);


namespace App\EconumoBundle\Domain\Factory;

use App\EconumoBundle\Domain\Entity\AccountAccessInvite;
use App\EconumoBundle\Domain\Entity\ValueObject\AccountUserRole;
use App\EconumoBundle\Domain\Entity\ValueObject\Id;

interface AccountAccessInviteFactoryInterface
{
    public function create(Id $accountId, Id $recipientId, Id $ownerId, AccountUserRole $role): AccountAccessInvite;
}
