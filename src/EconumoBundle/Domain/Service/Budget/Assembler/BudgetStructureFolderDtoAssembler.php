<?php

declare(strict_types=1);


namespace App\EconumoBundle\Domain\Service\Budget\Assembler;


use App\EconumoBundle\Domain\Entity\BudgetFolder;
use App\EconumoBundle\Domain\Service\Budget\Dto\BudgetStructureFolderDto;

readonly class BudgetStructureFolderDtoAssembler
{
    public function assemble(BudgetFolder $folder): BudgetStructureFolderDto
    {
        return new BudgetStructureFolderDto(
            $folder->getId(),
            $folder->getName(),
            $folder->getPosition()
        );
    }
}
