<?php

declare(strict_types=1);


namespace App\EconumoBundle\Domain\Service\Budget\Dto;


use App\EconumoBundle\Domain\Service\Budget\Dto\BudgetStructureFolderDto;
use App\EconumoBundle\Domain\Service\Budget\Dto\BudgetStructureParentElementDto;

readonly class BudgetStructureDto
{
    public function __construct(
        /** @var BudgetStructureFolderDto[] */
        public array $folders,
        /** @var BudgetStructureParentElementDto[] */
        public array $elements,
    ) {
    }
}
