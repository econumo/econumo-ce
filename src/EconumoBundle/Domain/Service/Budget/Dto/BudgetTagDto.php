<?php

declare(strict_types=1);

namespace App\EconumoBundle\Domain\Service\Budget\Dto;

use App\EconumoBundle\Domain\Entity\ValueObject\CategoryName;
use App\EconumoBundle\Domain\Entity\ValueObject\CategoryType;
use App\EconumoBundle\Domain\Entity\ValueObject\Icon;
use App\EconumoBundle\Domain\Entity\ValueObject\Id;
use App\EconumoBundle\Domain\Entity\ValueObject\TagName;

readonly class BudgetTagDto
{
    public function __construct(
        public Id $tagId,
        public Id $ownerUserId,
        public Id $budgetId,
        public ?Id $budgetFolderId,
        public ?Id $currencyId,
        public TagName $name,
        public Icon $icon,
        public int $position,
        public bool $isArchived,
    ) {
    }
}