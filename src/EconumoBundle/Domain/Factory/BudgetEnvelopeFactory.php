<?php

declare(strict_types=1);


namespace App\EconumoBundle\Domain\Factory;

use App\EconumoBundle\Domain\Entity\BudgetEnvelope;
use App\EconumoBundle\Domain\Entity\ValueObject\BudgetEnvelopeName;
use App\EconumoBundle\Domain\Entity\ValueObject\Icon;
use App\EconumoBundle\Domain\Entity\ValueObject\Id;
use App\EconumoBundle\Domain\Repository\BudgetRepositoryInterface;
use App\EconumoBundle\Domain\Repository\CategoryRepositoryInterface;
use App\EconumoBundle\Domain\Service\DatetimeServiceInterface;

readonly class BudgetEnvelopeFactory implements BudgetEnvelopeFactoryInterface
{
    public function __construct(
        private DatetimeServiceInterface $datetimeService,
        private BudgetRepositoryInterface $budgetRepository,
        private CategoryRepositoryInterface $categoryRepository,
    ) {
    }

    public function create(
        Id $budgetId,
        Id $id,
        BudgetEnvelopeName $name,
        Icon $icon,
        array $categoriesIds
    ): BudgetEnvelope {
        $categories = [];
        foreach ($categoriesIds as $categoryId) {
            $categories[] = $this->categoryRepository->getReference($categoryId);
        }

        return new BudgetEnvelope(
            $id,
            $this->budgetRepository->getReference($budgetId),
            $name,
            $icon,
            $categories,
            $this->datetimeService->getCurrentDatetime()
        );
    }
}
