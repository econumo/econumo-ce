<?php

declare(strict_types=1);

namespace App\EconumoBundle\Domain\Factory;

use App\EconumoBundle\Domain\Entity\Account;
use App\EconumoBundle\Domain\Entity\ValueObject\AccountName;
use App\EconumoBundle\Domain\Entity\ValueObject\AccountType;
use App\EconumoBundle\Domain\Entity\ValueObject\Icon;
use App\EconumoBundle\Domain\Entity\ValueObject\Id;

interface AccountFactoryInterface
{
    public function create(
        Id $userId,
        AccountName $name,
        AccountType $accountType,
        Id $currencyId,
        Icon $icon
    ): Account;
}
