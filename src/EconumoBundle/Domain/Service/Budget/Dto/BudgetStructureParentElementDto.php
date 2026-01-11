<?php

declare(strict_types=1);


namespace App\EconumoBundle\Domain\Service\Budget\Dto;


use App\EconumoBundle\Domain\Entity\ValueObject\BudgetElementType;
use App\EconumoBundle\Domain\Entity\ValueObject\Icon;
use App\EconumoBundle\Domain\Entity\ValueObject\Id;
use App\EconumoBundle\Domain\Entity\ValueObject\NameInterface;
use App\EconumoBundle\Domain\Entity\ValueObject\DecimalNumber;

readonly class BudgetStructureParentElementDto
{
    public function __construct(
        public Id $id,
        public BudgetElementType $type,
        public NameInterface $name,
        public Icon $icon,
        public ?Id $ownerId,
        public Id $currencyId,
        public bool $isArchived,
        public ?Id $folderId,
        public int $position,
        public DecimalNumber $budgeted,
        public DecimalNumber $available,
        public DecimalNumber $spent,
        public DecimalNumber $spentInBudgetCurrency,
        /** @var BudgetElementAmountSpentDto[] */
        public array $currenciesSpent,
        /** @var BudgetStructureChildElementDto[] */
        public array $children
    ) {
    }
}
