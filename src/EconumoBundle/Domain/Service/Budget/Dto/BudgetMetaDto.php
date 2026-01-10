<?php

declare(strict_types=1);

namespace App\EconumoBundle\Domain\Service\Budget\Dto;

use App\EconumoBundle\Domain\Entity\BudgetAccess;
use App\EconumoBundle\Domain\Entity\ValueObject\BudgetName;
use App\EconumoBundle\Domain\Entity\ValueObject\Id;
use DateTimeInterface;

readonly class BudgetMetaDto
{
    public function __construct(
        public Id $id,
        public Id $ownerUserId,
        public BudgetName $budgetName,
        public DateTimeInterface $startedAt,
        public Id $currencyId,
        /** @var BudgetUserAccessDto[] */
        public array $access
    ) {
    }
}