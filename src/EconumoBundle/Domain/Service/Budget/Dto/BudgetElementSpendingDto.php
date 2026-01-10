<?php

declare(strict_types=1);

namespace App\EconumoBundle\Domain\Service\Budget\Dto;

use App\EconumoBundle\Domain\Entity\ValueObject\BudgetElementType;
use App\EconumoBundle\Domain\Entity\ValueObject\Id;

class BudgetElementSpendingDto
{
    public function __construct(
        public readonly Id $elementId,
        public readonly BudgetElementType $elementType,
        /** @var BudgetElementSpendingCategoryDto[] */
        public array $spendingInCategories = []
    ) {
    }
}
