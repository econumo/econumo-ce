<?php

declare(strict_types=1);


namespace App\EconumoBundle\Domain\Service\Budget;


use App\EconumoBundle\Domain\Entity\ValueObject\BudgetFolderName;
use App\EconumoBundle\Domain\Entity\ValueObject\Id;
use App\EconumoBundle\Domain\Service\Budget\Dto\BudgetStructureFolderDto;

interface BudgetFolderServiceInterface
{
    public function create(Id $budgetId, Id $folderId, BudgetFolderName $name): BudgetStructureFolderDto;

    public function update(Id $budgetId, Id $folderId, BudgetFolderName $name): BudgetStructureFolderDto;

    public function delete(Id $budgetId, Id $folderId): void;
}
