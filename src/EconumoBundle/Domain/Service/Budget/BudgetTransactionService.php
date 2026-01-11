<?php

declare(strict_types=1);

namespace App\EconumoBundle\Domain\Service\Budget;

use App\EconumoBundle\Domain\Entity\ValueObject\Id;
use App\EconumoBundle\Domain\Exception\AccessDeniedException;
use App\EconumoBundle\Domain\Repository\BudgetEnvelopeRepositoryInterface;
use App\EconumoBundle\Domain\Repository\BudgetRepositoryInterface;
use App\EconumoBundle\Domain\Repository\CategoryRepositoryInterface;
use App\EconumoBundle\Domain\Repository\TagRepositoryInterface;
use App\EconumoBundle\Domain\Repository\TransactionRepositoryInterface;
use App\EconumoBundle\Domain\Service\Budget\Builder\BudgetFiltersBuilder;
use DateTimeImmutable;
use DateTimeInterface;

readonly class BudgetTransactionService implements BudgetTransactionServiceInterface
{
    public function __construct(
        private TransactionRepositoryInterface $transactionRepository,
        private BudgetFiltersBuilder $budgetFiltersBuilder,
        private BudgetRepositoryInterface $budgetRepository,
        private BudgetEnvelopeRepositoryInterface $budgetEnvelopeRepository,
        private CategoryRepositoryInterface $categoryRepository,
        private TagRepositoryInterface $tagRepository,
    ) {
    }

    /**
     * @inheritDoc
     */
    public function getTransactionsForCategory(
        Id $userId,
        Id $budgetId,
        DateTimeInterface $periodStart,
        Id $categoryId
    ): array {
        [$periodStart, $periodEnd] = $this->getPeriod($periodStart);
        $budget = $this->budgetRepository->get($budgetId);
        $budgetFilters = $this->budgetFiltersBuilder->build($budget, $userId, $periodStart, $periodEnd);
        $this->checkCategoryAccess($categoryId, $budgetFilters->userIds);

        return $this->transactionRepository->getByCategoriesIdsForAccountsIds(
            [$categoryId],
            $periodStart,
            $periodEnd,
            $budgetFilters->includedAccountsIds
        );
    }

    /**
     * @inheritDoc
     */
    public function getTransactionsForTag(
        Id $userId,
        Id $budgetId,
        DateTimeInterface $periodStart,
        Id $tagId,
        ?Id $categoryId
    ): array {
        // @TODO group tags with the same name
        [$periodStart, $periodEnd] = $this->getPeriod($periodStart);
        $budget = $this->budgetRepository->get($budgetId);
        $budgetFilters = $this->budgetFiltersBuilder->build($budget, $userId, $periodStart, $periodEnd);

        $this->checkTagAccess($tagId, $budgetFilters->userIds);
        $categoriesFilter = [];
        if ($categoryId instanceof Id) {
            $this->checkCategoryAccess($categoryId, $budgetFilters->userIds);
            $categoriesFilter[] = $categoryId;
        }

        return $this->transactionRepository->getByTagsIdsForAccountsIds(
            [$tagId],
            $periodStart,
            $periodEnd,
            $budgetFilters->includedAccountsIds,
            $categoriesFilter
        );
    }

    /**
     * @inheritDoc
     */
    public function getTransactionsForEnvelope(
        Id $userId,
        Id $budgetId,
        DateTimeInterface $periodStart,
        Id $envelopeId
    ): array {
        $envelope = $this->budgetEnvelopeRepository->get($envelopeId);
        if (!$envelope->getBudget()->getId()->isEqual($budgetId)) {
            throw new AccessDeniedException();
        }

        $categoriesIds = [];
        foreach ($envelope->getCategories() as $category) {
            $categoriesIds[] = $category->getId();
        }

        [$periodStart, $periodEnd] = $this->getPeriod($periodStart);
        $budget = $this->budgetRepository->get($budgetId);
        $budgetFilters = $this->budgetFiltersBuilder->build($budget, $userId, $periodStart, $periodEnd);

        return $this->transactionRepository->getByCategoriesIdsForAccountsIds(
            $categoriesIds,
            $periodStart,
            $periodEnd,
            $budgetFilters->includedAccountsIds
        );
    }

    /**
     * @return DateTimeInterface[]
     */
    private function getPeriod(DateTimeInterface $periodStart): array
    {
        $periodStart = DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $periodStart->format('Y-m-01 00:00:00'));
        $periodEnd = $periodStart->modify('next month');

        return [$periodStart, $periodEnd];
    }

    /**
     * @param Id[] $usersIds
     */
    private function checkCategoryAccess(Id $categoryId, array $usersIds): void
    {
        $category = $this->categoryRepository->get($categoryId);
        $isCategoryOwnedByBudgetUsers = false;
        foreach ($usersIds as $userId) {
            if ($category->getUserId()->isEqual($userId)) {
                $isCategoryOwnedByBudgetUsers = true;
            }
        }

        if (!$isCategoryOwnedByBudgetUsers) {
            throw new AccessDeniedException();
        }
    }

    /**
     * @param Id[] $usersIds
     */
    private function checkTagAccess(Id $tagId, array $usersIds): void
    {
        $tag = $this->tagRepository->get($tagId);
        $isTagOwnedByBudgetUsers = false;
        foreach ($usersIds as $userId) {
            if ($tag->getUserId()->isEqual($userId)) {
                $isTagOwnedByBudgetUsers = true;
            }
        }

        if (!$isTagOwnedByBudgetUsers) {
            throw new AccessDeniedException();
        }
    }
}
