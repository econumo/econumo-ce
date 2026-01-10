<?php

declare(strict_types=1);

namespace App\EconumoBundle\Domain\Service\Budget\Dto;

use App\EconumoBundle\Domain\Entity\ValueObject\BudgetEnvelopeName;
use App\EconumoBundle\Domain\Entity\ValueObject\Icon;
use App\EconumoBundle\Domain\Entity\ValueObject\Id;

readonly class BudgetEnvelopeDto
{
    public function __construct(
        public Id $id,
        public ?Id $currencyId,
        public BudgetEnvelopeName $name,
        public Icon $icon,
        public int $position,
        public bool $isArchived,
        /** @var Id[] */
        public array $categories
    ) {
    }
}