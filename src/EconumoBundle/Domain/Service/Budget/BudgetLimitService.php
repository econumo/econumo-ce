<?php

declare(strict_types=1);


namespace App\EconumoBundle\Domain\Service\Budget;


use App\EconumoBundle\Domain\Entity\BudgetElementLimit;
use App\EconumoBundle\Domain\Entity\ValueObject\Id;
use App\EconumoBundle\Domain\Entity\ValueObject\DecimalNumber;
use App\EconumoBundle\Domain\Exception\BudgetLimitInvalidDateException;
use App\EconumoBundle\Domain\Factory\BudgetElementLimitFactoryInterface;
use App\EconumoBundle\Domain\Repository\BudgetElementLimitRepositoryInterface;
use App\EconumoBundle\Domain\Repository\BudgetElementRepositoryInterface;
use App\EconumoBundle\Domain\Repository\BudgetRepositoryInterface;
use DateTimeInterface;

readonly class BudgetLimitService implements BudgetLimitServiceInterface
{
    public function __construct(
        private BudgetElementLimitRepositoryInterface $budgetElementLimitRepository,
        private BudgetElementLimitFactoryInterface $budgetElementLimitFactory,
        private BudgetElementRepositoryInterface $budgetElementRepository,
        private BudgetRepositoryInterface $budgetRepository,
    ) {
    }

    public function setLimit(Id $budgetId, Id $elementId, DateTimeInterface $period, ?DecimalNumber $amount): void
    {
        $budget = $this->budgetRepository->get($budgetId);
        if ($budget->getStartedAt() > $period) {
            throw new BudgetLimitInvalidDateException(sprintf('Budget started at %s', $budget->getStartedAt()->format('Y-m-d')));
        }

        $element = $this->budgetElementRepository->get($budgetId, $elementId);
        $elementLimit = $this->budgetElementLimitRepository->get($element->getId(), $period);
        if (!$amount instanceof DecimalNumber) {
            if ($elementLimit instanceof BudgetElementLimit) {
                $this->budgetElementLimitRepository->delete([$elementLimit]);
            }
        } else {
            if (!$elementLimit instanceof BudgetElementLimit) {
                $elementLimit = $this->budgetElementLimitFactory->create(
                    $element,
                    $amount,
                    $period
                );
            } else {
                $elementLimit->updateAmount($amount);
            }

            $this->budgetElementLimitRepository->save([$elementLimit]);
        }
    }
}
