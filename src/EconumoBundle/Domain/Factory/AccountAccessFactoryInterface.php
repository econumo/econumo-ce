<?php

declare(strict_types=1);


namespace App\EconumoBundle\Domain\Factory;

use App\EconumoBundle\Domain\Entity\AccountAccess;
use App\EconumoBundle\Domain\Entity\ValueObject\AccountUserRole;
use App\EconumoBundle\Domain\Entity\ValueObject\Id;

interface AccountAccessFactoryInterface
{
    public function create(Id $accountId, Id $userId, AccountUserRole $role): AccountAccess;
}
