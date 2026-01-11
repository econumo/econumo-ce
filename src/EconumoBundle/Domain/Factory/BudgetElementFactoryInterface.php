<?php

declare(strict_types=1);


namespace App\EconumoBundle\Domain\Factory;


use App\EconumoBundle\Domain\Entity\BudgetElement;
use App\EconumoBundle\Domain\Entity\ValueObject\BudgetElementType;
use App\EconumoBundle\Domain\Entity\ValueObject\Id;

interface BudgetElementFactoryInterface
{
    public function create(
        Id $budgetId,
        Id $elementId,
        BudgetElementType $elementType,
        int $position,
        ?Id $currencyId,
        ?Id $folderId
    ): BudgetElement;

    public function createCategoryElement(
        Id $budgetId,
        Id $categoryId,
        int $position,
        ?Id $currencyId = null,
        ?Id $folderId = null
    ): BudgetElement;

    public function createTagElement(
        Id $budgetId,
        Id $tagId,
        int $position,
        ?Id $currencyId = null,
        ?Id $folderId = null
    ): BudgetElement;

    public function createEnvelopeElement(
        Id $budgetId,
        Id $envelopeId,
        int $position,
        ?Id $currencyId = null,
        ?Id $folderId = null
    ): BudgetElement;
}
