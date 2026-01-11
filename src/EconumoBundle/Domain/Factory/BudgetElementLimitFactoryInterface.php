<?php

declare(strict_types=1);


namespace App\EconumoBundle\Domain\Factory;


use App\EconumoBundle\Domain\Entity\BudgetElement;
use App\EconumoBundle\Domain\Entity\BudgetElementLimit;
use App\EconumoBundle\Domain\Entity\ValueObject\DecimalNumber;
use DateTimeInterface;

interface BudgetElementLimitFactoryInterface
{
    public function create(
        BudgetElement $budgetElement,
        DecimalNumber $amount,
        DateTimeInterface $period
    ): BudgetElementLimit;
}
