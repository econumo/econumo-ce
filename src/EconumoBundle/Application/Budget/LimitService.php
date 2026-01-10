<?php

declare(strict_types=1);

namespace App\EconumoBundle\Application\Budget;

use App\EconumoBundle\Application\Budget\Dto\SetLimitV1RequestDto;
use App\EconumoBundle\Application\Budget\Dto\SetLimitV1ResultDto;
use App\EconumoBundle\Application\Budget\Assembler\SetLimitV1ResultAssembler;
use App\EconumoBundle\Application\Exception\AccessDeniedException;
use App\EconumoBundle\Application\Exception\ValidationException;
use App\EconumoBundle\Domain\Entity\ValueObject\Id;
use App\EconumoBundle\Domain\Entity\ValueObject\DecimalNumber;
use App\EconumoBundle\Domain\Exception\BudgetLimitInvalidDateException;
use App\EconumoBundle\Domain\Service\Budget\BudgetAccessServiceInterface;
use App\EconumoBundle\Domain\Service\Budget\BudgetLimitServiceInterface;
use DateTimeImmutable;

readonly class LimitService
{
    public function __construct(
        private BudgetAccessServiceInterface $budgetAccessService,
        private SetLimitV1ResultAssembler $setLimitV1ResultAssembler,
        private BudgetLimitServiceInterface $budgetLimitService
    ) {
    }

    public function setLimit(
        SetLimitV1RequestDto $dto,
        Id $userId
    ): SetLimitV1ResultDto {
        $budgetId = new Id($dto->budgetId);
        if (!$this->budgetAccessService->canUpdateBudget($userId, $budgetId)) {
            throw new AccessDeniedException();
        }

        $elementId = new Id($dto->elementId);
        $period = DateTimeImmutable::createFromFormat('Y-m-d', $dto->period);
        $amount = $dto->amount === null ? null : new DecimalNumber($dto->amount);

        try {
            $this->budgetLimitService->setLimit($budgetId, $elementId, $period, $amount);
        } catch (BudgetLimitInvalidDateException $budgetLimitInvalidDateException) {
            throw new ValidationException($budgetLimitInvalidDateException->getMessage());
        }

        return $this->setLimitV1ResultAssembler->assemble();
    }
}
