<?php

declare(strict_types=1);

namespace App\EconumoBundle\Domain\Service\Budget\Builder;

use App\EconumoBundle\Domain\Entity\Account;
use App\EconumoBundle\Domain\Entity\Budget;
use App\EconumoBundle\Domain\Entity\Category;
use App\EconumoBundle\Domain\Entity\Tag;
use App\EconumoBundle\Domain\Entity\ValueObject\Id;
use App\EconumoBundle\Domain\Repository\AccountRepositoryInterface;
use App\EconumoBundle\Domain\Repository\CategoryRepositoryInterface;
use App\EconumoBundle\Domain\Repository\TagRepositoryInterface;
use App\EconumoBundle\Domain\Service\Budget\Dto\BudgetFiltersDto;
use DateTimeInterface;

readonly class BudgetFiltersBuilder
{
    public function __construct(
        private CategoryRepositoryInterface $categoryRepository,
        private TagRepositoryInterface $tagRepository,
        private AccountRepositoryInterface $accountRepository,
    ) {
    }

    public function build(
        Budget $budget,
        Id $userId,
        DateTimeInterface $periodStart,
        DateTimeInterface $periodEnd
    ): BudgetFiltersDto {
        $excludedAccountIds = $this->getExcludedAccountIds($budget, $userId);
        $userIds = $this->getBudgetUserIds($budget);
        $includedAccounts = $this->getIncludedUserAccounts($budget, $userIds);
        $includedAccountsIds = $this->getIncludedUserAccountsIds($includedAccounts);
        $currenciesIds = $this->getCurrenciesIds($includedAccounts);
        $categories = $this->getCategories($userIds);
        $tags = $this->getTags($userIds);

        return new BudgetFiltersDto(
            $periodStart,
            $periodEnd,
            $userIds,
            $excludedAccountIds,
            $includedAccountsIds,
            $currenciesIds,
            $categories,
            $tags
        );
    }

    private function getExcludedAccountIds(Budget $budget, Id $userId): array
    {
        return array_map(static fn(Account $account): Id => $account->getId(), $budget->getExcludedAccounts($userId)->toArray());
    }

    public function getBudgetUserIds(Budget $budget): array
    {
        $userIds = [$budget->getUser()->getId()];
        foreach ($budget->getAccessList() as $entry) {
            if ($entry->isAccepted() && !$entry->getRole()->isReader()) {
                $userIds[] = $entry->getUserId();
            }
        }

        return $userIds;
    }

    /**
     * @param Id[] $userIds
     * @return Account[]
     */
    private function getIncludedUserAccounts(Budget $budget, array $userIds): array
    {
        $excludedAccounts = array_map(static fn(Account $account): string => $account->getId()->getValue(), $budget->getExcludedAccounts()->toArray());
        $userAccounts = $this->accountRepository->findByOwnersIds($userIds);
        $result = [];
        foreach ($userAccounts as $account) {
            if (!in_array($account->getId()->getValue(), $excludedAccounts)) {
                $result[] = $account;
            }
        }

        return $result;
    }

    /**
     * @param Account[] $userAccounts
     * @return Id[]
     */
    private function getIncludedUserAccountsIds(array $userAccounts): array
    {
        return array_map(static fn(Account $account): Id => $account->getId(), $userAccounts);
    }

    /**
     * @param Account[] $userAccounts
     * @return Id[]
     */
    private function getCurrenciesIds(array $userAccounts): array
    {
        $tmpCurrencies = [];
        foreach ($userAccounts as $account) {
            $tmpCurrencies[$account->getCurrencyId()->getValue()] = $account->getCurrencyId();
        }

        return array_values($tmpCurrencies);
    }

    /**
     * @return Category[]
     */
    public function getCategories(array $userIds): array
    {
        $result = [];
        foreach ($this->categoryRepository->findByOwnersIds($userIds) as $category) {
            if ($category->getType()->isExpense()) {
                $result[$category->getId()->getValue()] = $category;
            }
        }

        return $result;
    }

    /**
     * @return Tag[]
     */
    public function getTags(array $userIds): array
    {
        $result = [];
        foreach ($this->tagRepository->findByOwnersIds($userIds) as $tag) {
            $result[$tag->getId()->getValue()] = $tag;
        }

        return $result;
    }
}