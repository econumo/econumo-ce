<?php

declare(strict_types=1);

namespace App\EconumoBundle\Domain\Factory;

use App\EconumoBundle\Domain\Entity\AccountOptions;
use App\EconumoBundle\Domain\Entity\ValueObject\Id;

interface AccountOptionsFactoryInterface
{
    public function create(
        Id $accountId,
        Id $userId,
        int $position
    ): AccountOptions;
}
