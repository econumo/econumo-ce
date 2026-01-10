<?php

declare(strict_types=1);

namespace App\EconumoBundle\Domain\Service;

use App\EconumoBundle\Domain\Entity\Category;
use App\EconumoBundle\Domain\Entity\ValueObject\CategoryName;
use App\EconumoBundle\Domain\Entity\ValueObject\CategoryType;
use App\EconumoBundle\Domain\Entity\ValueObject\Icon;
use App\EconumoBundle\Domain\Entity\ValueObject\Id;
use App\EconumoBundle\Domain\Exception\CategoryAlreadyExistsException;
use App\EconumoBundle\Domain\Exception\ReplaceCategoryException;
use App\EconumoBundle\Domain\Factory\CategoryFactoryInterface;
use App\EconumoBundle\Domain\Repository\AccountRepositoryInterface;
use App\EconumoBundle\Domain\Repository\CategoryRepositoryInterface;
use App\EconumoBundle\Domain\Repository\TransactionRepositoryInterface;
use App\EconumoBundle\Domain\Service\AntiCorruptionServiceInterface;
use App\EconumoBundle\Domain\Service\Budget\BudgetElementServiceInterface;
use App\EconumoBundle\Domain\Service\CategoryServiceInterface;
use DateTimeInterface;
use Throwable;

readonly class CategoryService implements CategoryServiceInterface
{
    public function __construct(
        private CategoryFactoryInterface $categoryFactory,
        private CategoryRepositoryInterface $categoryRepository,
        private AccountRepositoryInterface $accountRepository,
        private AntiCorruptionServiceInterface $antiCorruptionService,
        private TransactionRepositoryInterface $transactionRepository,
        private BudgetElementServiceInterface $budgetElementService,
    ) {
    }

    public function createCategory(Id $userId, CategoryName $name, CategoryType $type, Icon $icon): Category
    {
        $categories = $this->categoryRepository->findByOwnerId($userId);
        foreach ($categories as $category) {
            if ($category->getName()->isEqual($name)) {
                throw new CategoryAlreadyExistsException();
            }
        }

        $this->antiCorruptionService->beginTransaction(__METHOD__);
        try {
            $category = $this->categoryFactory->create($userId, $name, $type, $icon);
            $category->updatePosition(count($categories));
            $this->categoryRepository->save([$category]);
            $this->budgetElementService->createCategoryElements($category);

            $this->antiCorruptionService->commit(__METHOD__);
        } catch (Throwable $throwable) {
            $this->antiCorruptionService->rollback(__METHOD__);
            throw $throwable;
        }

        return $category;
    }

    public function createCategoryForAccount(
        Id $userId,
        Id $accountId,
        CategoryName $name,
        CategoryType $type,
        Icon $icon
    ): Category {
        $account = $this->accountRepository->get($accountId);
        if ($userId->isEqual($account->getUserId())) {
            return $this->createCategory($userId, $name, $type, $icon);
        }

        return $this->createCategory($account->getUserId(), $name, $type, $icon);
    }

    public function deleteCategory(Id $categoryId): void
    {
        $this->antiCorruptionService->beginTransaction(__METHOD__);
        try {
            $category = $this->categoryRepository->get($categoryId);
            $this->budgetElementService->deleteCategoryElements($categoryId);
            $this->categoryRepository->delete($category);
            $this->antiCorruptionService->commit(__METHOD__);
        } catch (Throwable $throwable) {
            $this->antiCorruptionService->rollback(__METHOD__);
            throw $throwable;
        }
    }

    public function replaceCategory(Id $categoryId, Id $newCategoryId): void
    {
        $category = $this->categoryRepository->get($categoryId);
        $newCategory = $this->categoryRepository->get($newCategoryId);
        if (!$category->getUserId()->isEqual($newCategory->getUserId())) {
            throw new ReplaceCategoryException();
        }

        if (!$category->getType()->isEqual($newCategory->getType())) {
            throw new ReplaceCategoryException();
        }

        $this->antiCorruptionService->beginTransaction(__METHOD__);
        try {
            $this->transactionRepository->replaceCategory($categoryId, $newCategoryId);
            $this->deleteCategory($categoryId);
            $this->antiCorruptionService->commit(__METHOD__);
        } catch (Throwable $throwable) {
            $this->antiCorruptionService->rollback(__METHOD__);
            throw $throwable;
        }
    }

    public function orderCategories(Id $userId, array $changes): void
    {
        $categories = $this->categoryRepository->findByOwnerId($userId);
        $changed = [];
        foreach ($categories as $category) {
            foreach ($changes as $change) {
                if ($category->getId()->isEqual($change->getId())) {
                    $category->updatePosition($change->position);
                    $changed[] = $category;
                    break;
                }
            }
        }

        if ($changed === []) {
            return;
        }

        $this->categoryRepository->save($changed);
    }

    public function update(Id $categoryId, CategoryName $name, Icon $icon): void
    {
        $category = $this->categoryRepository->get($categoryId);
        $userCategories = $this->categoryRepository->findByOwnerId($category->getUserId());
        foreach ($userCategories as $userCategory) {
            if ($userCategory->getName()->isEqual($name) && !$userCategory->getId()->isEqual($categoryId)) {
                throw new CategoryAlreadyExistsException();
            }
        }

        $category->updateName($name);
        $category->updateIcon($icon);

        $this->categoryRepository->save([$category]);
    }

    public function archive(Id $categoryId): void
    {
        $this->antiCorruptionService->beginTransaction(__METHOD__);
        try {
            $category = $this->categoryRepository->get($categoryId);
            $category->archive();
            $this->categoryRepository->save([$category]);
            $this->budgetElementService->archiveCategoryElements($categoryId);
            $this->antiCorruptionService->commit(__METHOD__);
        } catch (Throwable $throwable) {
            $this->antiCorruptionService->rollback(__METHOD__);
            throw $throwable;
        }
    }

    public function unarchive(Id $categoryId): void
    {
        $this->antiCorruptionService->beginTransaction(__METHOD__);
        try {
            $category = $this->categoryRepository->get($categoryId);
            $category->unarchive();
            $this->categoryRepository->save([$category]);
            $this->budgetElementService->unarchiveCategoryElements($categoryId);
            $this->antiCorruptionService->commit(__METHOD__);
        } catch (Throwable $throwable) {
            $this->antiCorruptionService->rollback(__METHOD__);
            throw $throwable;
        }
    }

    public function getChanged(Id $userId, DateTimeInterface $lastUpdate): array
    {
        $categories = $this->categoryRepository->findAvailableForUserId($userId);
        $result = [];
        foreach ($categories as $category) {
            if ($category->getUpdatedAt() > $lastUpdate) {
                $result[] = $category;
            }
        }

        return $result;
    }
}
