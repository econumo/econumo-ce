<?php

declare(strict_types=1);


namespace App\EconumoBundle\Domain\Service\Budget;


use App\EconumoBundle\Domain\Entity\ValueObject\Id;
use App\EconumoBundle\Domain\Service\Budget\Dto\BudgetEnvelopeDto;
use App\EconumoBundle\Domain\Service\Budget\Dto\BudgetStructureParentElementDto;

interface BudgetEnvelopeServiceInterface
{
    public function create(Id $budgetId, BudgetEnvelopeDto $envelope, ?Id $folderId = null): BudgetStructureParentElementDto;

    public function update(Id $budgetId, BudgetEnvelopeDto $envelopeDto): BudgetStructureParentElementDto;

    public function delete(Id $budgetId, Id $envelopeId): void;
}
