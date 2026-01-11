<?php

declare(strict_types=1);

namespace App\EconumoBundle\Application\Budget;

use App\EconumoBundle\Application\Budget\Dto\ChangeElementCurrencyV1RequestDto;
use App\EconumoBundle\Application\Budget\Dto\ChangeElementCurrencyV1ResultDto;
use App\EconumoBundle\Application\Budget\Assembler\ChangeElementCurrencyV1ResultAssembler;
use App\EconumoBundle\Application\Exception\AccessDeniedException;
use App\EconumoBundle\Domain\Entity\ValueObject\Id;
use App\EconumoBundle\Domain\Service\Budget\BudgetAccessServiceInterface;
use App\EconumoBundle\Domain\Service\Budget\BudgetElementServiceInterface;

readonly class ElementCurrencyService
{
    public function __construct(
        private ChangeElementCurrencyV1ResultAssembler $changeElementCurrencyV1ResultAssembler,
        private BudgetAccessServiceInterface $budgetAccessService,
        private BudgetElementServiceInterface $budgetElementService,
    ) {
    }

    public function changeElementCurrency(
        ChangeElementCurrencyV1RequestDto $dto,
        Id $userId
    ): ChangeElementCurrencyV1ResultDto {
        $budgetId = new Id($dto->budgetId);
        if (!$this->budgetAccessService->canUpdateBudget($userId, $budgetId)) {
            throw new AccessDeniedException();
        }

        $elementId = new Id($dto->elementId);
        $currencyId = new Id($dto->currencyId);
        $this->budgetElementService->changeElementCurrency($budgetId, $elementId, $currencyId);
        return $this->changeElementCurrencyV1ResultAssembler->assemble();
    }
}
