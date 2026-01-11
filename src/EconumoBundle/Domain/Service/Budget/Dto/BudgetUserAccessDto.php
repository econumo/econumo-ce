<?php

declare(strict_types=1);

namespace App\EconumoBundle\Domain\Service\Budget\Dto;

use App\EconumoBundle\Domain\Entity\ValueObject\Id;
use App\EconumoBundle\Domain\Entity\ValueObject\UserName;
use App\EconumoBundle\Domain\Entity\ValueObject\UserRole;

readonly class BudgetUserAccessDto
{
    public function __construct(
        public Id $id,
        public UserName $name,
        public string $avatar,
        public UserRole $role,
        public bool $isAccepted
    ) {
    }
}
