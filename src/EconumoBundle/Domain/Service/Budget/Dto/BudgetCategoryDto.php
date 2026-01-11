<?php

declare(strict_types=1);

namespace App\EconumoBundle\Domain\Service\Budget\Dto;

use App\EconumoBundle\Domain\Entity\ValueObject\CategoryName;
use App\EconumoBundle\Domain\Entity\ValueObject\CategoryType;
use App\EconumoBundle\Domain\Entity\ValueObject\Icon;
use App\EconumoBundle\Domain\Entity\ValueObject\Id;

readonly class BudgetCategoryDto
{
    public function __construct(
        public Id $categoryId,
        public Id $ownerUserId,
        public Id $budgetId,
        public ?Id $budgetFolderId,
        public ?Id $currencyId,
        public CategoryName $name,
        public CategoryType $type,
        public Icon $icon,
        public int $position,
        public bool $isArchived,
    ) {
    }
}