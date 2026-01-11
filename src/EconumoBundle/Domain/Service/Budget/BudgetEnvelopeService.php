<?php

declare(strict_types=1);


namespace App\EconumoBundle\Domain\Service\Budget;


use App\EconumoBundle\Domain\Entity\Budget;
use App\EconumoBundle\Domain\Entity\BudgetElement;
use App\EconumoBundle\Domain\Entity\BudgetEnvelope;
use App\EconumoBundle\Domain\Entity\BudgetFolder;
use App\EconumoBundle\Domain\Entity\Category;
use App\EconumoBundle\Domain\Entity\Currency;
use App\EconumoBundle\Domain\Entity\ValueObject\BudgetElementType;
use App\EconumoBundle\Domain\Entity\ValueObject\Id;
use App\EconumoBundle\Domain\Entity\ValueObject\DecimalNumber;
use App\EconumoBundle\Domain\Exception\AccessDeniedException;
use App\EconumoBundle\Domain\Factory\BudgetElementFactoryInterface;
use App\EconumoBundle\Domain\Factory\BudgetElementLimitFactoryInterface;
use App\EconumoBundle\Domain\Factory\BudgetEnvelopeFactoryInterface;
use App\EconumoBundle\Domain\Repository\BudgetElementLimitRepositoryInterface;
use App\EconumoBundle\Domain\Repository\BudgetElementRepositoryInterface;
use App\EconumoBundle\Domain\Repository\BudgetEnvelopeRepositoryInterface;
use App\EconumoBundle\Domain\Repository\BudgetRepositoryInterface;
use App\EconumoBundle\Domain\Repository\CurrencyRepositoryInterface;
use App\EconumoBundle\Domain\Service\AntiCorruptionServiceInterface;
use App\EconumoBundle\Domain\Service\Budget\Builder\BudgetFiltersBuilder;
use App\EconumoBundle\Domain\Service\Budget\Dto\BudgetEnvelopeDto;
use App\EconumoBundle\Domain\Service\Budget\Dto\BudgetStructureChildElementDto;
use App\EconumoBundle\Domain\Service\Budget\Dto\BudgetStructureParentElementDto;
use DateTime;
use Throwable;

readonly class BudgetEnvelopeService implements BudgetEnvelopeServiceInterface
{
    public function __construct(
        private BudgetElementsActionsService $budgetElementsActionsService,
        private BudgetEnvelopeFactoryInterface $budgetEnvelopeFactory,
        private BudgetElementFactoryInterface $budgetElementFactory,
        private BudgetRepositoryInterface $budgetRepository,
        private AntiCorruptionServiceInterface $antiCorruptionService,
        private BudgetEnvelopeRepositoryInterface $budgetEnvelopeRepository,
        private BudgetElementRepositoryInterface $budgetElementRepository,
        private BudgetFiltersBuilder $budgetFiltersBuilder,
        private BudgetElementLimitRepositoryInterface $budgetElementLimitRepository,
        private BudgetElementLimitFactoryInterface $budgetElementLimitFactory,
        private CurrencyRepositoryInterface $currencyRepository,
        private BudgetElementServiceInterface $budgetElementService,
    ) {
    }

    public function create(
        Id $budgetId,
        BudgetEnvelopeDto $envelope,
        ?Id $folderId = null
    ): BudgetStructureParentElementDto {
        $this->antiCorruptionService->beginTransaction(__METHOD__);
        try {
            $budget = $this->budgetRepository->get($budgetId);
            $envelopeCategoriesMap = [];
            if ($envelope->categories !== []) {
                $envelopeCategoriesMap = $this->getEligibleCategories($budget, $envelope->categories);
                $this->budgetEnvelopeRepository->deleteAssociationsWithCategories($budgetId, $envelope->categories);
            }

            $this->budgetElementsActionsService->shiftElements($budgetId, $folderId, $envelope->position);
            $envelopeEntity = $this->budgetEnvelopeFactory->create(
                $budgetId,
                $envelope->id,
                $envelope->name,
                $envelope->icon,
                []
            );
            $this->budgetEnvelopeRepository->save([$envelopeEntity]);
            if ($envelope->categories !== []) {
                $this->includeCategoriesToEnvelope($envelopeEntity, $envelopeCategoriesMap);
            }

            $envelopeCurrencyId = $envelope->currencyId;
            if ($envelopeCurrencyId instanceof Id && $budget->getCurrencyId()->isEqual($envelope->currencyId)) {
                $envelopeCurrencyId = null;
            }

            $envelopeElement = $this->budgetElementFactory->createEnvelopeElement(
                $budgetId,
                $envelope->id,
                $envelope->position,
                $envelopeCurrencyId,
                $folderId
            );
            $this->budgetElementRepository->save([$envelopeElement]);

            if ($envelope->categories !== []) {
                $this->reassignAmounts($budgetId, $envelopeEntity);
            }

            $this->antiCorruptionService->commit(__METHOD__);
        } catch (Throwable $throwable) {
            $this->antiCorruptionService->rollback(__METHOD__);
            throw $throwable;
        }

        $this->budgetElementsActionsService->restoreElementsOrder($budgetId);

        return $this->assembleEnvelope($envelopeEntity, $envelopeElement, $budget);
    }

    public function update(Id $budgetId, BudgetEnvelopeDto $envelopeDto): BudgetStructureParentElementDto
    {
        $this->antiCorruptionService->beginTransaction(__METHOD__);
        try {
            $budget = $this->budgetRepository->get($budgetId);
            $envelope = $this->budgetEnvelopeRepository->get($envelopeDto->id);
            $envelopeElement = $this->budgetElementRepository->get($budgetId, $envelope->getId());

            if (!$envelope->isArchived() && $envelopeDto->isArchived) {
                $this->budgetElementService->archiveEnvelopeElement($envelope->getId());
            } elseif ($envelope->isArchived() && !$envelopeDto->isArchived) {
                $this->budgetElementService->unarchiveEnvelopeElement($envelope->getId());
            }

            $envelope->updateName($envelopeDto->name);
            $envelope->updateIcon($envelopeDto->icon);
            $envelope->setArchive($envelopeDto->isArchived);
            $this->budgetEnvelopeRepository->save([$envelope]);

            if (!$envelopeDto->currencyId instanceof Id
                || ($envelopeDto->currencyId && $budget->getCurrency()->getId()->isEqual($envelopeDto->currencyId))) {
                $envelopeElement->updateCurrency(null);
            } else {
                $currency = $this->currencyRepository->get($envelopeDto->currencyId);
                $envelopeElement->updateCurrency($currency);
            }

            $this->budgetElementRepository->save([$envelopeElement]);

            if ($envelopeDto->categories !== [] || $envelope->getCategories()->count() > 0) {
                $envelopeCategoriesMap = [];
                if ($envelopeDto->categories !== []) {
                    $envelopeCategoriesMap = $this->getEligibleCategories($budget, $envelopeDto->categories);
                }

                $oldCategories = [];
                $categoriesToRemoveFromEnvelope = [];
                foreach ($envelope->getCategories() as $category) {
                    $oldCategories[$category->getId()->getValue()] = $category;
                    if (!array_key_exists($category->getId()->getValue(), $envelopeCategoriesMap)) {
                        $categoriesToRemoveFromEnvelope[$category->getId()->getValue()] = $category;
                    }
                }

                if ($categoriesToRemoveFromEnvelope !== []) {
                    $this->removeCategoriesFromEnvelope($envelope, $categoriesToRemoveFromEnvelope);
                }

                $newCategories = array_diff(array_keys($envelopeCategoriesMap), array_keys($oldCategories));
                $newCategoriesIds = [];
                foreach ($newCategories as $category) {
                    $newCategoriesIds[] = $envelopeCategoriesMap[$category]->getId();
                }

                $this->budgetEnvelopeRepository->deleteAssociationsWithCategories($budgetId, $newCategoriesIds);
                $this->includeCategoriesToEnvelope($envelope, $envelopeCategoriesMap);
            }

            $this->antiCorruptionService->commit(__METHOD__);
        } catch (Throwable $throwable) {
            $this->antiCorruptionService->rollback(__METHOD__);
            throw $throwable;
        }

        $this->budgetElementsActionsService->restoreElementsOrder($budgetId);

        return $this->assembleEnvelope($envelope, $envelopeElement, $budget);
    }

    private function reassignAmounts(Id $budgetId, BudgetEnvelope $envelope): void
    {
        $this->antiCorruptionService->beginTransaction(__METHOD__);
        try {
            $element = $this->budgetElementRepository->get($budgetId, $envelope->getId());
            $categoriesIds = [];
            foreach ($envelope->getCategories() as $category) {
                $categoriesIds[] = $category->getId();
            }

            $originLimits = $this->budgetElementLimitRepository
                ->getSummarizedAmountsForElements($budgetId, $categoriesIds);
            $targetLimits = $this->budgetElementLimitRepository
                ->getByBudgetIdAndElementId($budgetId, $envelope->getId());

            $updated = [];
            $seen = [];
            foreach ($targetLimits as $targetAmount) {
                $date = $targetAmount->getPeriod()->format('Y-m-d');
                $seen[] = $date;
                if (array_key_exists($date, $originLimits)) {
                    $targetAmount->updateAmount($targetAmount->getAmount()->add($originLimits[$date]));
                    $updated[] = $targetAmount;
                }
            }

            if ($updated !== []) {
                $this->budgetElementLimitRepository->save($updated);
            }

            $keysToCreate = array_diff(array_keys($originLimits), $seen);
            $created = [];
            foreach ($keysToCreate as $key) {
                $created[] = $this->budgetElementLimitFactory->create(
                    $element,
                    new DecimalNumber($originLimits[$key]),
                    DateTime::createFromFormat('Y-m-d', $key)
                );
            }

            if ($created !== []) {
                $this->budgetElementLimitRepository->save($created);
            }

            $toDelete = [];
            foreach ($envelope->getCategories() as $category) {
                $oldAmounts = $this->budgetElementLimitRepository
                    ->getByBudgetIdAndElementId($budgetId, $category->getId());
                if ($oldAmounts !== []) {
                    $toDelete = array_merge($toDelete, $oldAmounts);
                }
            }

            if ($toDelete !== []) {
                $this->budgetElementLimitRepository->delete($toDelete);
            }

            $this->antiCorruptionService->commit(__METHOD__);
        } catch (Throwable $throwable) {
            $this->antiCorruptionService->rollback(__METHOD__);
            throw $throwable;
        }
    }

    private function assembleEnvelope(
        BudgetEnvelope $envelope,
        BudgetElement $envelopeOption,
        Budget $budget
    ): BudgetStructureParentElementDto {
        $children = [];
        foreach ($envelope->getCategories() as $category) {
            $children[] = new BudgetStructureChildElementDto(
                $category->getId(),
                BudgetElementType::category(),
                $category->getName(),
                $category->getIcon(),
                $category->getUserId(),
                $category->isArchived(),
                new DecimalNumber(),
                new DecimalNumber(),
                []
            );
        }

        return new BudgetStructureParentElementDto(
            $envelope->getId(),
            $envelopeOption->getType(),
            $envelope->getName(),
            $envelope->getIcon(),
            null,
            ($envelopeOption->getCurrency() instanceof Currency ? $envelopeOption->getCurrency()->getId(
            ) : $budget->getCurrencyId()),
            $envelope->isArchived(),
            $envelopeOption->getFolder()?->getId(),
            $envelopeOption->getPosition(),
            new DecimalNumber(),
            new DecimalNumber(),
            new DecimalNumber(),
            new DecimalNumber(),
            [],
            $children
        );
    }

    /**
     * @param Id[] $categoriesIds
     * @return Category[]
     */
    private function getEligibleCategories(Budget $budget, array $categoriesIds): array
    {
        $result = [];
        $budgetUserIds = $this->budgetFiltersBuilder->getBudgetUserIds($budget);
        $availableCategories = $this->budgetFiltersBuilder->getCategories($budgetUserIds);
        foreach ($categoriesIds as $category) {
            if (!array_key_exists($category->getValue(), $availableCategories)) {
                throw new AccessDeniedException();
            }

            $result[$category->getValue()] = $availableCategories[$category->getValue()];
        }

        return $result;
    }

    /**
     * @param Category[] $categoriesMap
     */
    private function includeCategoriesToEnvelope(BudgetEnvelope $envelope, array $categoriesMap): void
    {
        if ($categoriesMap === []) {
            return;
        }

        foreach ($categoriesMap as $category) {
            $envelope->addCategory($category);
        }

        $this->budgetEnvelopeRepository->save([$envelope]);

        $updatedOptions = [];
        $budgetElementsOptions = $this->budgetElementRepository->getByBudgetId($envelope->getBudget()->getId());
        foreach ($budgetElementsOptions as $budgetElementOption) {
            if (!$budgetElementOption->getType()->isCategory()) {
                continue;
            }

            if (!array_key_exists($budgetElementOption->getExternalId()->getValue(), $categoriesMap)) {
                continue;
            }

            $budgetElementOption->unsetPosition();
            $budgetElementOption->changeFolder(null);
            $updatedOptions[] = $budgetElementOption;
        }

        if ($updatedOptions !== []) {
            $this->budgetElementRepository->save($updatedOptions);
        }
    }

    /**
     * @param Category[] $categoriesMap
     */
    private function removeCategoriesFromEnvelope(BudgetEnvelope $envelope, array $categoriesMap): void
    {
        if ($categoriesMap === []) {
            return;
        }

        foreach ($categoriesMap as $category) {
            $envelope->removeCategory($category);
        }

        $toUpdate = [];
        $budgetElementsOptions = $this->budgetElementRepository->getByBudgetId($envelope->getBudget()->getId());
        $envelopeOptions = null;
        foreach ($budgetElementsOptions as $budgetElementOption) {
            if ($budgetElementOption->getExternalId()->isEqual($envelope->getId())
                && $budgetElementOption->getType()->isEnvelope()) {
                $envelopeOptions = $budgetElementOption;
                break;
            }
        }

        $envelopePosition = 0;
        $envelopeFolder = null;
        $envelopeFolderId = null;
        if ($envelopeOptions instanceof BudgetElement) {
            $envelopeFolder = $envelopeOptions->getFolder();
            $envelopeFolderId = $envelopeOptions->getFolder()?->getId();
            $envelopePosition = $envelopeOptions->getPosition();
        }

        $shiftTo = count($categoriesMap);
        $position = $envelopePosition;
        foreach ($budgetElementsOptions as $budgetElementOption) {
            if (!$budgetElementOption->getType()->isCategory()) {
                continue;
            }

            if (array_key_exists($budgetElementOption->getExternalId()->getValue(), $categoriesMap)) {
                $budgetElementOption->changeFolder($envelopeFolder);
                $budgetElementOption->updatePosition(++$position);
                $toUpdate[] = $budgetElementOption;
            } elseif ($budgetElementOption->getPosition() > $envelopePosition) {
                if ((!$budgetElementOption->getFolder() instanceof BudgetFolder && !$envelopeFolder instanceof BudgetFolder)
                    || ($budgetElementOption->getFolder() instanceof BudgetFolder && $envelopeFolder instanceof BudgetFolder
                        && $budgetElementOption->getFolder()->getId()->isEqual($envelopeFolderId))) {
                    $budgetElementOption->updatePosition($budgetElementOption->getPosition() + $shiftTo);
                    $toUpdate[] = $budgetElementOption;
                }
            }
        }

        if ($toUpdate !== []) {
            $this->budgetElementRepository->save($toUpdate);
        }
    }

    public function delete(Id $budgetId, Id $envelopeId): void
    {
        $envelope = $this->budgetEnvelopeRepository->get($envelopeId);
        if (!$envelope->getBudget()->getId()->isEqual($budgetId)) {
            throw new AccessDeniedException('Envelope was not found for this budget');
        }

        $this->antiCorruptionService->beginTransaction(__METHOD__);
        try {
            $categoriesMap = [];
            foreach ($envelope->getCategories() as $category) {
                $categoriesMap[$category->getId()->getValue()] = $category;
            }

            $this->removeCategoriesFromEnvelope($envelope, $categoriesMap);
            $this->budgetElementService->deleteEnvelopeElement($envelope->getId());
            $this->budgetEnvelopeRepository->delete([$envelope]);
            $this->antiCorruptionService->commit(__METHOD__);
        } catch (Throwable $throwable) {
            $this->antiCorruptionService->rollback(__METHOD__);
            throw $throwable;
        }

        $this->budgetElementsActionsService->restoreElementsOrder($budgetId);
    }
}
