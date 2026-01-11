<?php

declare(strict_types=1);

namespace App\EconumoBundle\Domain\Factory;

use App\EconumoBundle\Domain\Entity\BudgetFolder;
use App\EconumoBundle\Domain\Entity\ValueObject\BudgetFolderName;
use App\EconumoBundle\Domain\Entity\ValueObject\Id;

interface BudgetFolderFactoryInterface
{
    /**
     * @param Id $budgetId
     * @param Id $folderId
     * @param BudgetFolderName $name
     * @return BudgetFolder
     */
    public function create(
        Id $budgetId,
        Id $folderId,
        BudgetFolderName $name
    ): BudgetFolder;
}
