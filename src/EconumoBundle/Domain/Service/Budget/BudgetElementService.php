<?php

declare(strict_types=1);

namespace App\EconumoBundle\Domain\Service\Budget;

use App\EconumoBundle\Domain\Entity\BudgetElement;
use App\EconumoBundle\Domain\Entity\Category;
use App\EconumoBundle\Domain\Entity\Tag;
use App\EconumoBundle\Domain\Entity\ValueObject\Id;
use App\EconumoBundle\Domain\Factory\BudgetElementFactoryInterface;
use App\EconumoBundle\Domain\Repository\BudgetElementRepositoryInterface;
use App\EconumoBundle\Domain\Repository\BudgetRepositoryInterface;
use App\EconumoBundle\Domain\Repository\CategoryRepositoryInterface;
use App\EconumoBundle\Domain\Repository\CurrencyRepositoryInterface;
use App\EconumoBundle\Domain\Repository\TagRepositoryInterface;
use App\EconumoBundle\Domain\Service\Budget\Dto\BudgetCategoryDto;
use App\EconumoBundle\Domain\Service\Budget\Dto\BudgetTagDto;

readonly class BudgetElementService implements BudgetElementServiceInterface
{
    public function __construct(
        private CategoryRepositoryInterface $categoryRepository,
        private TagRepositoryInterface $tagRepository,
        private BudgetElementFactoryInterface $budgetElementFactory,
        private BudgetElementRepositoryInterface $budgetElementRepository,
        private BudgetRepositoryInterface $budgetRepository,
        private BudgetElementsActionsService  $budgetElementsActionsService,
        private CurrencyRepositoryInterface $currencyRepository,
    ) {
    }

    /**
     * @param Id $userId
     * @param Id $budgetId
     * @param int $startPosition
     * @return array [int, BudgetCategoryDto[]]
     */
    public function createCategoriesElements(Id $userId, Id $budgetId, int $startPosition = 0): array
    {
        $result = [];
        $categories = $this->categoryRepository->findByOwnerId($userId);
        $position = $startPosition;
        $entities = [];
        foreach ($categories as $category) {
            if ($category->getType()->isIncome()) {
                continue;
            }

            $item = $this->budgetElementFactory->createCategoryElement(
                $budgetId,
                $category->getId(),
                ($category->isArchived() ? BudgetElement::POSITION_UNSET : $position++)
            );
            $entities[] = $item;
            $result[] = new BudgetCategoryDto(
                $category->getId(),
                $category->getUserId(),
                $budgetId,
                null,
                null,
                $category->getName(),
                $category->getType(),
                $category->getIcon(),
                $item->getPosition(),
                $category->isArchived()
            );
        }

        $this->budgetElementRepository->save($entities);

        return [$position, $result];
    }

    /**
     * @param Id $userId
     * @param Id $budgetId
     * @param int $startPosition
     * @return array [int, BudgetTagDto[]]
     */
    public function createTagsElements(Id $userId, Id $budgetId, int $startPosition = 0): array
    {
        $result = [];
        $position = $startPosition;
        $tags = $this->tagRepository->findByOwnerId($userId);
        $entities = [];
        foreach ($tags as $tag) {
            $item = $this->budgetElementFactory->createTagElement(
                $budgetId,
                $tag->getId(),
                ($tag->isArchived() ? BudgetElement::POSITION_UNSET : $position++)
            );
            $entities[] = $item;
            $result[] = new BudgetTagDto(
                $tag->getId(),
                $tag->getUserId(),
                $budgetId,
                null,
                null,
                $tag->getName(),
                $tag->getIcon(),
                $item->getPosition(),
                $tag->isArchived()
            );
        }

        $this->budgetElementRepository->save($entities);

        return [$position, $result];
    }

    public function createCategoryElements(Category $category): void
    {
        if ($category->getType()->isIncome()) {
            return;
        }

        $newElements = [];
        $budgets = $this->budgetRepository->getByUserId($category->getUserId());
        foreach ($budgets as $budget) {
            if ($category->isArchived()) {
                $position = BudgetElement::POSITION_UNSET;
            } else {
                $position = $this->budgetElementRepository->getNextPosition($budget->getId(), null);
            }

            $item = $this->budgetElementFactory->createCategoryElement(
                $budget->getId(),
                $category->getId(),
                $position
            );
            $newElements[] = $item;
        }

        $this->budgetElementRepository->save($newElements);
    }

    public function deleteCategoryElements(Id $categoryId): void
    {
        $this->deleteElements($categoryId);
    }

    public function archiveCategoryElements(Id $categoryId): void
    {
        $this->archiveElements($categoryId);
    }

    public function unarchiveCategoryElements(Id $categoryId): void
    {
        $this->unarchiveElements($categoryId);
    }

    public function createTagElements(Tag $tag): void
    {
        $newElements = [];
        $budgets = $this->budgetRepository->getByUserId($tag->getUserId());
        foreach ($budgets as $budget) {
            if ($tag->isArchived()) {
                $position = BudgetElement::POSITION_UNSET;
            } else {
                $position = $this->budgetElementRepository->getNextPosition($budget->getId(), null);
            }

            $item = $this->budgetElementFactory->createTagElement(
                $budget->getId(),
                $tag->getId(),
                $position
            );
            $newElements[] = $item;
        }

        $this->budgetElementRepository->save($newElements);
    }

    public function deleteTagElements(Id $tagId): void
    {
        $this->deleteElements($tagId);
    }

    public function archiveTagElements(Id $tagId): void
    {
        $this->archiveElements($tagId);
    }

    public function unarchiveTagElements(Id $tagId): void
    {
        $this->unarchiveElements($tagId);
    }

    private function deleteElements(Id $externalId): void
    {
        $elements = $this->budgetElementRepository->getElementsByExternalId($externalId);
        foreach ($elements as $element) {
//            $this->budgetElementLimitRepository->deleteByElementId($element->getId());
            $this->budgetElementRepository->delete([$element]);
            $this->budgetElementsActionsService->restoreElementsOrder($element->getBudget()->getId());
        }
    }

    private function archiveElements(Id $externalId): void
    {
        $elements = $this->budgetElementRepository->getElementsByExternalId($externalId);
        foreach ($elements as $element) {
            $element->unsetPosition();
            $element->changeFolder(null);
            $this->budgetElementRepository->save([$element]);
            $this->budgetElementsActionsService->restoreElementsOrder($element->getBudget()->getId());
        }
    }

    private function unarchiveElements(Id $externalId): void
    {
        $elements = $this->budgetElementRepository->getElementsByExternalId($externalId);
        foreach ($elements as $element) {
            $position = $this->budgetElementRepository->getNextPosition($element->getBudget()->getId(), null);
            $element->updatePosition($position);
            $element->changeFolder(null);
        }

        $this->budgetElementRepository->save($elements);
    }

    public function deleteEnvelopeElement(Id $envelopeId): void
    {
        $this->deleteElements($envelopeId);
    }

    public function archiveEnvelopeElement(Id $envelopeId): void
    {
        $this->archiveElements($envelopeId);
    }

    public function unarchiveEnvelopeElement(Id $envelopeId): void
    {
        $this->unarchiveElements($envelopeId);
    }

    public function deleteCategoriesElements(Id $userId, Id $budgetId): void
    {
        $categories = $this->categoryRepository->findByOwnerId($userId);
        $ids = [];
        foreach ($categories as $category) {
            $ids[] = $category->getId();
        }

        $this->budgetElementsActionsService->deleteElements($budgetId, $ids);
    }

    public function deleteTagsElements(Id $userId, Id $budgetId): void
    {
        $tags = $this->tagRepository->findByOwnerId($userId);
        $ids = [];
        foreach ($tags as $tag) {
            $ids[] = $tag->getId();
        }

        $this->budgetElementsActionsService->deleteElements($budgetId, $ids);
    }

    public function changeElementCurrency(Id $budgetId, Id $elementId, Id $currencyId): void
    {
        $element = $this->budgetElementRepository->get($budgetId, $elementId);
        $currency = $this->currencyRepository->get($currencyId);
        $element->updateCurrency($currency);
        $this->budgetElementRepository->save([$element]);
    }
}
