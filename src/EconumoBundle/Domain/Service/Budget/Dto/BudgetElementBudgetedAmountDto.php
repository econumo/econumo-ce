<?php

declare(strict_types=1);

namespace App\EconumoBundle\Domain\Service\Budget\Dto;

use App\EconumoBundle\Domain\Entity\ValueObject\BudgetElementType;
use App\EconumoBundle\Domain\Entity\ValueObject\Id;
use App\EconumoBundle\Domain\Entity\ValueObject\DecimalNumber;

readonly class BudgetElementBudgetedAmountDto
{
    public function __construct(
        public Id $elementId,
        public BudgetElementType $elementType,
        public ?DecimalNumber $budgeted,
        public DecimalNumber $budgetedBefore
    ) {
    }
}
