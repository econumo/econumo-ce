<?php

declare(strict_types=1);


namespace App\EconumoBundle\Domain\Factory;


use App\EconumoBundle\Domain\Entity\BudgetElement;
use App\EconumoBundle\Domain\Entity\BudgetElementLimit;
use App\EconumoBundle\Domain\Entity\ValueObject\DecimalNumber;
use App\EconumoBundle\Domain\Repository\BudgetElementLimitRepositoryInterface;
use App\EconumoBundle\Domain\Service\DatetimeServiceInterface;
use DateTimeInterface;

readonly class BudgetElementLimitFactory implements BudgetElementLimitFactoryInterface
{
    public function __construct(
        private DatetimeServiceInterface $datetimeService,
        private BudgetElementLimitRepositoryInterface $budgetElementLimitRepository,
    ) {
    }

    public function create(
        BudgetElement $budgetElement,
        DecimalNumber $amount,
        DateTimeInterface $period
    ): BudgetElementLimit {
        return new BudgetElementLimit(
            $this->budgetElementLimitRepository->getNextIdentity(),
            $budgetElement,
            $amount,
            $period,
            $this->datetimeService->getCurrentDatetime()
        );
    }
}
