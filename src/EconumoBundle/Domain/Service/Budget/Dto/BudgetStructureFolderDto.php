<?php

declare(strict_types=1);


namespace App\EconumoBundle\Domain\Service\Budget\Dto;


use App\EconumoBundle\Domain\Entity\ValueObject\BudgetFolderName;
use App\EconumoBundle\Domain\Entity\ValueObject\Id;

readonly class BudgetStructureFolderDto
{
    public function __construct(
        public Id $id,
        public BudgetFolderName $name,
        public int $position
    ) {
    }
}
