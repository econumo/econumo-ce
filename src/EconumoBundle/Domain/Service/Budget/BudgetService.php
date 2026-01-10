<?php

declare(strict_types=1);

namespace App\EconumoBundle\Domain\Service\Budget;

use App\EconumoBundle\Domain\Entity\ValueObject\BudgetName;
use App\EconumoBundle\Domain\Entity\ValueObject\Id;
use App\EconumoBundle\Domain\Exception\AccessDeniedException;
use App\EconumoBundle\Domain\Factory\BudgetFactoryInterface;
use App\EconumoBundle\Domain\Repository\AccountRepositoryInterface;
use App\EconumoBundle\Domain\Repository\BudgetElementLimitRepositoryInterface;
use App\EconumoBundle\Domain\Repository\BudgetRepositoryInterface;
use App\EconumoBundle\Domain\Repository\CurrencyRepositoryInterface;
use App\EconumoBundle\Domain\Repository\UserRepositoryInterface;
use App\EconumoBundle\Domain\Service\AntiCorruptionServiceInterface;
use App\EconumoBundle\Domain\Service\Budget\Builder\BudgetBuilder;
use App\EconumoBundle\Domain\Service\Budget\Builder\BudgetMetaBuilder;
use App\EconumoBundle\Domain\Service\Budget\Dto\BudgetDto;
use App\EconumoBundle\Domain\Service\Budget\Dto\BudgetMetaDto;
use App\EconumoBundle\Domain\Service\DatetimeServiceInterface;
use App\EconumoBundle\Domain\Service\UserServiceInterface;
use DateTimeInterface;
use Throwable;

readonly class BudgetService implements BudgetServiceInterface
{
    public function __construct(
        private BudgetFactoryInterface $budgetFactory,
        private BudgetRepositoryInterface $budgetRepository,
        private DatetimeServiceInterface $datetimeService,
        private UserServiceInterface $userService,
        private AntiCorruptionServiceInterface $antiCorruptionService,
        private BudgetElementServiceInterface $budgetElementService,
        private BudgetMetaBuilder $budgetMetaBuilder,
        private BudgetDeletionService $budgetDeletionService,
        private AccountRepositoryInterface $accountRepository,
        private BudgetElementLimitRepositoryInterface $budgetElementLimitRepository,
        private BudgetBuilder $budgetBuilder,
        private UserRepositoryInterface $userRepository,
        private CurrencyRepositoryInterface $currencyRepository,
        private BudgetUpdateService $budgetUpdateService,
        private BudgetElementsActionsService $budgetElementsActionsService,
        private BudgetFoldersService $budgetFoldersService
    ) {
    }

    public function createBudget(
        Id $userId,
        Id $budgetId,
        BudgetName $name,
        ?DateTimeInterface $startDate,
        ?Id $currencyId,
        array $excludedAccountsIds = []
    ): BudgetDto {
        $this->antiCorruptionService->beginTransaction(__METHOD__);
        try {
            $date = $startDate ?? $this->datetimeService->getCurrentDatetime();
            if (!$currencyId instanceof Id) {
                $user = $this->userRepository->get($userId);
                $currency = $this->currencyRepository->getByCode($user->getCurrency());
            } else {
                $currency = $this->currencyRepository->getReference($currencyId);
            }

            $budget = $this->budgetFactory->create(
                $userId,
                $budgetId,
                $name,
                $date,
                $currency->getId(),
                $excludedAccountsIds,
            );
            $this->budgetRepository->save([$budget]);
            [$position, $categoriesOptions] = $this->budgetElementService->createCategoriesElements($userId, $budgetId);
            $this->budgetElementService->createTagsElements($userId, $budgetId, $position);
            $this->userService->updateBudget($userId, $budgetId);
//            $this->userService->completeOnboarding($userId);

            $this->antiCorruptionService->commit(__METHOD__);
        } catch (Throwable $throwable) {
            $this->antiCorruptionService->rollback(__METHOD__);
            throw $throwable;
        }

        return $this->budgetBuilder->build($userId, $budget, $this->datetimeService->getCurrentDatetime());
    }

    public function getBudgetList(Id $userId): array
    {
        $budgets = $this->budgetRepository->getByUserId($userId);
        $result = [];
        foreach ($budgets as $budget) {
            $result[] = $this->budgetMetaBuilder->build($budget);
        }

        return $result;
    }

    public function deleteBudget(Id $budgetId): void
    {
        $this->budgetDeletionService->deleteBudget($budgetId);
    }

    /**
     * @inheritDoc
     */
    public function updateBudget(
        Id $userId,
        Id $budgetId,
        BudgetName $name,
        Id $currencyId,
        array $excludedAccountsIds = []
    ): BudgetMetaDto {
        return $this->budgetUpdateService->updateBudget($userId, $budgetId, $name, $currencyId, $excludedAccountsIds);
    }

    public function excludeAccount(Id $userId, Id $budgetId, Id $accountId): BudgetMetaDto
    {
        $account = $this->accountRepository->get($accountId);
        if (!$account->getUserId()->isEqual($userId)) {
            throw new AccessDeniedException();
        }

        $budget = $this->budgetRepository->get($budgetId);
        $budget->excludeAccount($account);

        $this->budgetRepository->save([$budget]);
        return $this->budgetMetaBuilder->build($budget);
    }

    public function includeAccount(Id $userId, Id $budgetId, Id $accountId): BudgetMetaDto
    {
        $account = $this->accountRepository->get($accountId);
        if (!$account->getUserId()->isEqual($userId)) {
            throw new AccessDeniedException();
        }

        $budget = $this->budgetRepository->get($budgetId);
        $budget->includeAccount($account);

        $this->budgetRepository->save([$budget]);
        return $this->budgetMetaBuilder->build($budget);
    }

    public function resetBudget(Id $userId, Id $budgetId, DateTimeInterface $startedAt): BudgetMetaDto
    {
        $budget = $this->budgetRepository->get($budgetId);
        try {
            $this->antiCorruptionService->beginTransaction(__METHOD__);
            $this->budgetElementLimitRepository->deleteByBudgetId($budgetId);

            $budget->startFrom($startedAt);
            $this->budgetRepository->save([$budget]);
            $this->antiCorruptionService->commit(__METHOD__);
        } catch (Throwable $throwable) {
            $this->antiCorruptionService->rollback(__METHOD__);
            throw $throwable;
        }

        return $this->budgetMetaBuilder->build($budget);
    }

    public function getBudget(Id $userId, Id $budgetId, DateTimeInterface $periodStart): BudgetDto
    {
        $budget = $this->budgetRepository->get($budgetId);

        return $this->budgetBuilder->build($userId, $budget, $periodStart);
    }

    public function moveElements(Id $userId, Id $budgetId, array $affectedElements): void
    {
        $budget = $this->budgetRepository->get($budgetId);
        $this->budgetElementsActionsService->moveElements($budget, $affectedElements);
    }

    public function orderFolders(Id $userId, Id $budgetId, array $affectedFolders): void
    {
        $this->budgetFoldersService->orderFolders($budgetId, $affectedFolders);
    }
}
