<?php

declare(strict_types=1);

namespace App\EconumoBundle\Domain\Service\Budget\Dto;

use App\EconumoBundle\Domain\Entity\ValueObject\Id;

class BudgetElementSpendingCategoryDto
{
    public function __construct(
        public readonly Id $categoryId,
        public readonly ?Id $tagId,
        /** @var BudgetElementAmountSpentDto[] */
        public array $currenciesSpent = [],
        /** @var BudgetElementAmountSpentDto[] */
        public array $currenciesSpentBefore = [],
    ) {
    }
}
