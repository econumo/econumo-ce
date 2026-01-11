<?php

declare(strict_types=1);


namespace App\EconumoBundle\Domain\Service\Budget\Dto;

use App\EconumoBundle\Domain\Entity\ValueObject\Id;

readonly class BudgetStructureMoveElementDto
{
    public function __construct(
        public Id $id,
        public int $position,
        public ?Id $folderId,
    ) {
    }
}
