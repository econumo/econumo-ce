<?php

declare(strict_types=1);


namespace App\EconumoBundle\Domain\Service\Budget\Dto;


use App\EconumoBundle\Domain\Entity\ValueObject\BudgetElementType;
use App\EconumoBundle\Domain\Entity\ValueObject\Icon;
use App\EconumoBundle\Domain\Entity\ValueObject\Id;
use App\EconumoBundle\Domain\Entity\ValueObject\NameInterface;
use App\EconumoBundle\Domain\Entity\ValueObject\DecimalNumber;

readonly class BudgetStructureChildElementDto
{
    public function __construct(
        public Id $id,
        public BudgetElementType $type,
        public NameInterface $name,
        public Icon $icon,
        public Id $ownerId,
        public bool $isArchived,
        public DecimalNumber $spent,
        public DecimalNumber $spentInBudgetCurrency,
        /** @var BudgetElementAmountSpentDto[] */
        public array $currenciesSpent,
    ) {
    }
}
