<?php

declare(strict_types=1);

namespace App\EconumoBundle\Domain\Service\Budget;

use App\EconumoBundle\Domain\Entity\Category;
use App\EconumoBundle\Domain\Entity\Tag;
use App\EconumoBundle\Domain\Entity\ValueObject\Id;

interface BudgetElementServiceInterface
{
    /**
     * @param Id $userId
     * @param Id $budgetId
     * @param int $startPosition
     * @return array [int, BudgetCategoryDto[]]
     */
    public function createCategoriesElements(Id $userId, Id $budgetId, int $startPosition = 0): array;

    public function deleteCategoriesElements(Id $userId, Id $budgetId): void;

    /**
     * @param Id $userId
     * @param Id $budgetId
     * @param int $startPosition
     * @return array [int, BudgetTagDto[]]
     */
    public function createTagsElements(Id $userId, Id $budgetId, int $startPosition = 0): array;

    public function deleteTagsElements(Id $userId, Id $budgetId): void;

    public function createCategoryElements(Category $category): void;

    public function deleteCategoryElements(Id $categoryId): void;

    public function archiveCategoryElements(Id $categoryId): void;

    public function unarchiveCategoryElements(Id $categoryId): void;

    public function createTagElements(Tag $tag): void;

    public function deleteTagElements(Id $tagId): void;

    public function archiveTagElements(Id $tagId): void;

    public function unarchiveTagElements(Id $tagId): void;

    public function deleteEnvelopeElement(Id $envelopeId): void;

    public function archiveEnvelopeElement(Id $envelopeId): void;

    public function unarchiveEnvelopeElement(Id $envelopeId): void;

    public function changeElementCurrency(Id $budgetId, Id $elementId, Id $currencyId): void;
}
