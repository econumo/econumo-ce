<?php

declare(strict_types=1);

namespace App\EconumoBundle\Application\Budget;

use App\EconumoBundle\Application\Budget\Dto\GrantAccessV1RequestDto;
use App\EconumoBundle\Application\Budget\Dto\GrantAccessV1ResultDto;
use App\EconumoBundle\Application\Budget\Assembler\GrantAccessV1ResultAssembler;
use App\EconumoBundle\Application\Exception\AccessDeniedException;
use App\EconumoBundle\Domain\Entity\ValueObject\BudgetUserRole;
use App\EconumoBundle\Domain\Entity\ValueObject\Id;
use App\EconumoBundle\Application\Budget\Dto\AcceptAccessV1RequestDto;
use App\EconumoBundle\Application\Budget\Dto\AcceptAccessV1ResultDto;
use App\EconumoBundle\Application\Budget\Assembler\AcceptAccessV1ResultAssembler;
use App\EconumoBundle\Domain\Service\Budget\BudgetAccessServiceInterface;
use App\EconumoBundle\Domain\Service\Budget\BudgetServiceInterface;
use App\EconumoBundle\Domain\Service\Budget\BudgetSharedAccessServiceInterface;
use App\EconumoBundle\Application\Budget\Dto\RevokeAccessV1RequestDto;
use App\EconumoBundle\Application\Budget\Dto\RevokeAccessV1ResultDto;
use App\EconumoBundle\Application\Budget\Assembler\RevokeAccessV1ResultAssembler;
use App\EconumoBundle\Application\Budget\Dto\DeclineAccessV1RequestDto;
use App\EconumoBundle\Application\Budget\Dto\DeclineAccessV1ResultDto;
use App\EconumoBundle\Application\Budget\Assembler\DeclineAccessV1ResultAssembler;

readonly class AccessService
{
    public function __construct(
        private GrantAccessV1ResultAssembler $grantAccessV1ResultAssembler,
        private AcceptAccessV1ResultAssembler $acceptAccessV1ResultAssembler,
        private BudgetAccessServiceInterface $budgetAccessService,
        private BudgetSharedAccessServiceInterface $budgetSharedAccessService,
        private BudgetServiceInterface $budgetService,
        private RevokeAccessV1ResultAssembler $revokeAccessV1ResultAssembler,
        private DeclineAccessV1ResultAssembler $declineAccessV1ResultAssembler,
    ) {
    }

    public function grantAccess(
        GrantAccessV1RequestDto $dto,
        Id $userId
    ): GrantAccessV1ResultDto {
        $budgetId = new Id($dto->budgetId);
        if (!$this->budgetAccessService->canShareBudget($userId, $budgetId)) {
            throw new AccessDeniedException();
        }

        $invitedUserId = new Id($dto->userId);
        $role = BudgetUserRole::createFromAlias($dto->role);

        $this->budgetSharedAccessService->grantAccess($userId, $budgetId, $invitedUserId, $role);
        $budgets = $this->budgetService->getBudgetList($userId);
        return $this->grantAccessV1ResultAssembler->assemble($budgets);
    }

    public function acceptAccess(
        AcceptAccessV1RequestDto $dto,
        Id $userId
    ): AcceptAccessV1ResultDto {
        $budgetId = new Id($dto->budgetId);
        if (!$this->budgetAccessService->canAcceptBudget($userId, $budgetId)) {
            throw new AccessDeniedException();
        }

        $this->budgetSharedAccessService->acceptAccess($budgetId, $userId);
        $budgets = $this->budgetService->getBudgetList($userId);
        return $this->acceptAccessV1ResultAssembler->assemble($budgets);
    }

    public function revokeAccess(
        RevokeAccessV1RequestDto $dto,
        Id $userId
    ): RevokeAccessV1ResultDto {
        $budgetId = new Id($dto->budgetId);
        if (!$this->budgetAccessService->canShareBudget($userId, $budgetId)) {
            throw new AccessDeniedException();
        }

        $invitedUserId = new Id($dto->userId);
        $this->budgetSharedAccessService->revokeAccess($budgetId, $invitedUserId);
        return $this->revokeAccessV1ResultAssembler->assemble();
    }

    public function declineAccess(
        DeclineAccessV1RequestDto $dto,
        Id $userId
    ): DeclineAccessV1ResultDto {
        $budgetId = new Id($dto->budgetId);
        if (!$this->budgetAccessService->canDeclineBudget($userId, $budgetId)) {
            throw new AccessDeniedException();
        }

        $this->budgetSharedAccessService->revokeAccess($budgetId, $userId);
        return $this->declineAccessV1ResultAssembler->assemble();
    }
}
