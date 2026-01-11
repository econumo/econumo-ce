<?php

declare(strict_types=1);


namespace App\EconumoBundle\Domain\Factory;


use App\EconumoBundle\Domain\Entity\BudgetAccess;
use App\EconumoBundle\Domain\Entity\ValueObject\BudgetUserRole;
use App\EconumoBundle\Domain\Entity\ValueObject\Id;

interface BudgetAccessFactoryInterface
{
    public function create(Id $budgetId, Id $userId, BudgetUserRole $role): BudgetAccess;
}
