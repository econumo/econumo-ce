<?php

declare(strict_types=1);


namespace App\EconumoBundle\Domain\Service\Budget;


use App\EconumoBundle\Domain\Entity\ValueObject\BudgetName;
use App\EconumoBundle\Domain\Entity\ValueObject\Id;
use App\EconumoBundle\Domain\Service\Budget\Dto\BudgetDto;
use App\EconumoBundle\Domain\Service\Budget\Dto\BudgetMetaDto;
use App\EconumoBundle\Domain\Service\Budget\Dto\BudgetStructureMoveElementDto;
use App\EconumoBundle\Domain\Service\Budget\Dto\BudgetStructureOrderItemDto;
use DateTimeInterface;

interface BudgetServiceInterface
{
    /**
     * @param Id $userId User ID
     * @param Id $budgetId Budget ID
     * @param BudgetName $name Budget name
     * @param DateTimeInterface|null $startDate
     * @param Id|null $currencyId
     * @param Id[] $excludedAccountsIds
     * @return BudgetDto
     */
    public function createBudget(
        Id $userId,
        Id $budgetId,
        BudgetName $name,
        ?DateTimeInterface $startDate,
        ?Id $currencyId,
        array $excludedAccountsIds = []
    ): BudgetDto;

    /**
     * @return BudgetMetaDto[]
     */
    public function getBudgetList(Id $userId): array;

    public function deleteBudget(Id $budgetId): void;

    /**
     * @param Id[] $excludedAccountsIds
     * @return BudgetMetaDto
     */
    public function updateBudget(
        Id $userId,
        Id $budgetId,
        BudgetName $name,
        Id $currencyId,
        array $excludedAccountsIds = []
    ): BudgetMetaDto;

    public function excludeAccount(Id $userId, Id $budgetId, Id $accountId): BudgetMetaDto;

    public function includeAccount(Id $userId, Id $budgetId, Id $accountId): BudgetMetaDto;

    public function resetBudget(Id $userId, Id $budgetId, DateTimeInterface $startedAt): BudgetMetaDto;

    public function getBudget(Id $userId, Id $budgetId, DateTimeInterface $periodStart): BudgetDto;

    /**
     * @param BudgetStructureMoveElementDto[] $affectedElements
     * @return void
     */
    public function moveElements(Id $userId, Id $budgetId, array $affectedElements): void;

    /**
     * @param BudgetStructureOrderItemDto[] $affectedFolders
     * @return void
     */
    public function orderFolders(Id $userId, Id $budgetId, array $affectedFolders): void;
}
