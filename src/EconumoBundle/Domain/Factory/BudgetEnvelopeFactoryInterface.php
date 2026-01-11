<?php

declare(strict_types=1);


namespace App\EconumoBundle\Domain\Factory;

use App\EconumoBundle\Domain\Entity\BudgetEnvelope;
use App\EconumoBundle\Domain\Entity\ValueObject\BudgetEnvelopeName;
use App\EconumoBundle\Domain\Entity\ValueObject\Icon;
use App\EconumoBundle\Domain\Entity\ValueObject\Id;

interface BudgetEnvelopeFactoryInterface
{
    /**
     * @param Id $budgetId
     * @param Id $id
     * @param BudgetEnvelopeName $name
     * @param Icon $icon
     * @param Id[] $categoriesIds
     * @return BudgetEnvelope
     */
    public function create(
        Id $budgetId,
        Id $id,
        BudgetEnvelopeName $name,
        Icon $icon,
        array $categoriesIds
    ): BudgetEnvelope;
}
