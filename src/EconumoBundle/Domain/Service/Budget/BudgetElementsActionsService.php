<?php

declare(strict_types=1);


namespace App\EconumoBundle\Domain\Service\Budget;


use App\EconumoBundle\Domain\Entity\Budget;
use App\EconumoBundle\Domain\Entity\BudgetFolder;
use App\EconumoBundle\Domain\Entity\ValueObject\BudgetElementType;
use App\EconumoBundle\Domain\Entity\ValueObject\Id;
use App\EconumoBundle\Domain\Factory\BudgetElementFactoryInterface;
use App\EconumoBundle\Domain\Repository\BudgetElementLimitRepositoryInterface;
use App\EconumoBundle\Domain\Repository\BudgetElementRepositoryInterface;
use App\EconumoBundle\Domain\Repository\BudgetEnvelopeRepositoryInterface;
use App\EconumoBundle\Domain\Repository\BudgetFolderRepositoryInterface;
use App\EconumoBundle\Domain\Repository\BudgetRepositoryInterface;
use App\EconumoBundle\Domain\Service\AntiCorruptionServiceInterface;
use App\EconumoBundle\Domain\Service\Budget\Builder\BudgetFiltersBuilder;
use App\EconumoBundle\Domain\Service\Budget\Dto\BudgetStructureMoveElementDto;
use Throwable;

readonly class BudgetElementsActionsService
{
    public function __construct(
        private BudgetElementRepositoryInterface $budgetElementRepository,
        private BudgetElementFactoryInterface $budgetElementFactory,
        private BudgetFolderRepositoryInterface $budgetFolderRepository,
        private BudgetFiltersBuilder $budgetFiltersBuilder,
        private BudgetEnvelopeRepositoryInterface $budgetEnvelopeRepository,
        private BudgetRepositoryInterface $budgetRepository,
        private BudgetElementLimitRepositoryInterface $budgetElementLimitRepository,
        private AntiCorruptionServiceInterface $antiCorruptionService
    ) {
    }

    /**
     * @param BudgetStructureMoveElementDto[] $elementsToMove
     */
    public function moveElements(Budget $budget, array $elementsToMove): void
    {
        $seen = [];
        $budgetElements = $this->budgetElementRepository->getByBudgetId($budget->getId());
        $updatedElements = [];
        foreach ($budgetElements as $option) {
            if (!array_key_exists($option->getExternalId()->getValue(), $elementsToMove)) {
                continue;
            }

            if (array_key_exists($option->getExternalId()->getValue(), $seen)) {
                continue;
            }

            $seen[$option->getExternalId()->getValue()] = true;

            $isUpdated = false;
            $element = $elementsToMove[$option->getExternalId()->getValue()];
            if (!$element->folderId instanceof Id && $option->getFolder() instanceof BudgetFolder) {
                $option->changeFolder(null);
                $isUpdated = true;
            } elseif ($element->folderId instanceof Id && !$option->getFolder() instanceof BudgetFolder) {
                $option->changeFolder($this->budgetFolderRepository->getReference($element->folderId));
                $isUpdated = true;
            } elseif ($element->folderId instanceof Id && $option->getFolder() instanceof BudgetFolder && !$option->getFolder()->getId(
                )->isEqual($element->folderId)) {
                $option->changeFolder($this->budgetFolderRepository->getReference($element->folderId));
                $isUpdated = true;
            }

            if ($element->position !== $option->getPosition()) {
                $option->updatePosition($element->position);
                $isUpdated = true;
            }

            if ($isUpdated) {
                $updatedElements[] = $option;
            }
        }

        if ($updatedElements !== []) {
            $this->budgetElementRepository->save($updatedElements);
        }

        $this->restoreElementsOrder($budget->getId());
    }

    public function restoreElementsOrder(Id $budgetId): void
    {
        $folders = $this->budgetFolderRepository->getByBudgetId($budgetId);
        $options = $this->budgetElementRepository->getByBudgetId($budgetId);

        $optionsAssoc = [];
        foreach ($options as $option) {
            $index = sprintf('%s_%s', $option->getExternalId()->getValue(), $option->getType()->getAlias());
            $optionsAssoc[$index] = $option;
        }

        $seen = [];
        $budget = $this->budgetRepository->get($budgetId);

        $envelopes = $this->budgetEnvelopeRepository->getByBudgetId($budgetId);
        $envelopeType = BudgetElementType::envelope()->getAlias();
        $childCategoriesMap = [];
        foreach ($envelopes as $envelope) {
            $envelopeIndex = sprintf('%s_%s', $envelope->getId()->getValue(), $envelopeType);
            $seen[$envelopeIndex] = true;
            if (!array_key_exists($envelopeIndex, $optionsAssoc)) {
                $optionsAssoc[$envelopeIndex] = $this->budgetElementFactory->createEnvelopeElement($budgetId, $envelope->getId(), PHP_INT_MAX);
                $options[] = $optionsAssoc[$envelopeIndex];
            }

            if ($envelope->isArchived()) {
                $optionsAssoc[$envelopeIndex]->unsetPosition();
            } elseif ($optionsAssoc[$envelopeIndex]->isPositionUnset()) {
                $optionsAssoc[$envelopeIndex]->updatePosition(PHP_INT_MAX);
            }

            foreach ($envelope->getCategories() as $category) {
                $childCategoriesMap[$category->getId()->getValue()] = $category;
            }
        }

        $budgetUserIds = $this->budgetFiltersBuilder->getBudgetUserIds($budget);
        $categories = $this->budgetFiltersBuilder->getCategories($budgetUserIds);
        $categoryType = BudgetElementType::category()->getAlias();
        foreach ($categories as $category) {
            $categoryIndex = sprintf('%s_%s', $category->getId()->getValue(), $categoryType);
            $seen[$categoryIndex] = true;
            if (!array_key_exists($categoryIndex, $optionsAssoc)) {
                $optionsAssoc[$categoryIndex] = $this->budgetElementFactory->createCategoryElement($budgetId, $category->getId(), PHP_INT_MAX);
                $options[] = $optionsAssoc[$categoryIndex];
            }

            if ($category->isArchived()) {
                $optionsAssoc[$categoryIndex]->unsetPosition();
            } elseif ($optionsAssoc[$categoryIndex]->isPositionUnset()) {
                $optionsAssoc[$categoryIndex]->updatePosition(PHP_INT_MAX);
            }

            if (array_key_exists($category->getId()->getValue(), $childCategoriesMap)) {
                $optionsAssoc[$categoryIndex]->unsetPosition();
                $optionsAssoc[$categoryIndex]->changeFolder(null);
            }
        }

        $tags = $this->budgetFiltersBuilder->getTags($budgetUserIds);
        $tagType = BudgetElementType::tag()->getAlias();
        foreach ($tags as $tag) {
            $tagIndex = sprintf('%s_%s', $tag->getId()->getValue(), $tagType);
            $seen[$tagIndex] = true;
            if (!array_key_exists($tagIndex, $optionsAssoc)) {
                $optionsAssoc[$tagIndex] = $this->budgetElementFactory->createTagElement($budgetId, $tag->getId(), PHP_INT_MAX);
                $options[] = $optionsAssoc[$tagIndex];
            }

            if ($tag->isArchived()) {
                $optionsAssoc[$tagIndex]->unsetPosition();
            } elseif ($optionsAssoc[$tagIndex]->isPositionUnset()) {
                $optionsAssoc[$tagIndex]->updatePosition(PHP_INT_MAX);
            }
        }

        // sorting inside folders
        foreach ($folders as $folder) {
            $position = 0;
            foreach ($options as $option) {
                if (!$option->getFolder() || !$option->getFolder()->getId()->isEqual($folder->getId())) {
                    continue;
                }

                if ($option->isPositionUnset()) {
                    continue;
                }

                $option->updatePosition($position++);
            }
        }

        // sorting inside folders
        $position = 0;
        foreach ($options as $option) {
            if ($option->getFolder() || $option->isPositionUnset()) {
                continue;
            }

            $option->updatePosition($position++);
        }

        $this->budgetElementRepository->save($options);

        $keysToDelete = array_diff(array_keys($optionsAssoc), array_keys($seen));
        $toDelete = [];
        foreach ($keysToDelete as $optionId) {
            $toDelete[] = $optionsAssoc[$optionId];
        }

        if ($toDelete !== []) {
            $this->budgetElementRepository->delete($toDelete);
        }
    }

    public function shiftElements(Id $budgetId, ?Id $folderId, int $startPosition): void
    {
        $elements = $this->budgetElementRepository->getByBudgetId($budgetId);
        $position = $startPosition;
        $updated = [];
        foreach ($elements as $option) {
            if (!$folderId instanceof Id && $option->getFolder() instanceof BudgetFolder) {
                continue;
            }

            if ($folderId instanceof Id && !$option->getFolder() instanceof BudgetFolder) {
                continue;
            }

            if ($folderId instanceof Id && $option->getFolder() instanceof BudgetFolder && !$option->getFolder()->getId()->isEqual($folderId)) {
                continue;
            }

            if ($option->getPosition() < $startPosition) {
                continue;
            }

            $option->updatePosition(++$position);
            $updated[] = $option;
        }

        if ($updated === []) {
            return;
        }

        $this->budgetElementRepository->save($updated);
    }

    /**
     * @param Id[] $elementsIds
     * @throws Throwable
     */
    public function deleteElements(Id $budgetId, array $elementsIds): void
    {
        $elementsIdsMap = [];
        foreach ($elementsIds as $elementId) {
            $elementsIdsMap[$elementId->getValue()] = $elementId;
        }

        $elements = $this->budgetElementRepository->getByBudgetId($budgetId);
        $toDelete = [];
        $toDeleteIds = [];
        foreach ($elements as $element) {
            $externalIdValue = $element->getExternalId()->getValue();
            if (array_key_exists($externalIdValue, $elementsIdsMap)) {
                $toDelete[] = $element;
                $toDeleteIds[] = $element->getId();
            }
        }

         if ($toDelete === []) {
             return;
         }

        $this->antiCorruptionService->beginTransaction(__METHOD__);
        try {
            $this->budgetElementLimitRepository->deleteByElementIds($toDeleteIds);
            $this->budgetElementRepository->delete($toDelete);
            $this->restoreElementsOrder($budgetId);
            $this->antiCorruptionService->commit(__METHOD__);
        } catch (Throwable $throwable) {
            $this->antiCorruptionService->rollback(__METHOD__);
            throw $throwable;
        }
    }
}
