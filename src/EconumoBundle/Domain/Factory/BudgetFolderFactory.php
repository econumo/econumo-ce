<?php

declare(strict_types=1);


namespace App\EconumoBundle\Domain\Factory;


use App\EconumoBundle\Domain\Entity\BudgetFolder;
use App\EconumoBundle\Domain\Entity\ValueObject\BudgetFolderName;
use App\EconumoBundle\Domain\Entity\ValueObject\Id;
use App\EconumoBundle\Domain\Repository\BudgetRepositoryInterface;
use App\EconumoBundle\Domain\Service\DatetimeServiceInterface;

readonly class BudgetFolderFactory implements BudgetFolderFactoryInterface
{
    public function __construct(
        private DatetimeServiceInterface $datetimeService,
        private BudgetRepositoryInterface $budgetRepository
    ) {
    }

    public function create(Id $budgetId, Id $folderId, BudgetFolderName $name): BudgetFolder
    {
        return new BudgetFolder(
            $folderId,
            $this->budgetRepository->getReference($budgetId),
            $name,
            0,
            $this->datetimeService->getCurrentDatetime()
        );
    }
}
