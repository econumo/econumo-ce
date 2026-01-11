<?php

declare(strict_types=1);

namespace App\EconumoBundle\Domain\Service\Budget\Builder;

use App\EconumoBundle\Domain\Entity\Budget;
use App\EconumoBundle\Domain\Entity\BudgetElement;
use App\EconumoBundle\Domain\Entity\BudgetEnvelope;
use App\EconumoBundle\Domain\Entity\BudgetFolder;
use App\EconumoBundle\Domain\Entity\ValueObject\BudgetElementType;
use App\EconumoBundle\Domain\Entity\ValueObject\Id;
use App\EconumoBundle\Domain\Entity\ValueObject\DecimalNumber;
use App\EconumoBundle\Domain\Repository\BudgetElementRepositoryInterface;
use App\EconumoBundle\Domain\Repository\BudgetEnvelopeRepositoryInterface;
use App\EconumoBundle\Domain\Repository\BudgetFolderRepositoryInterface;
use App\EconumoBundle\Domain\Service\Budget\Assembler\BudgetStructureFolderDtoAssembler;
use App\EconumoBundle\Domain\Service\Budget\Dto\BudgetElementAmountSpentDto;
use App\EconumoBundle\Domain\Service\Budget\Dto\BudgetElementBudgetedAmountDto;
use App\EconumoBundle\Domain\Service\Budget\Dto\BudgetElementSpendingDto;
use App\EconumoBundle\Domain\Service\Budget\Dto\BudgetFiltersDto;
use App\EconumoBundle\Domain\Service\Budget\Dto\BudgetStructureChildElementDto;
use App\EconumoBundle\Domain\Service\Budget\Dto\BudgetStructureDto;
use App\EconumoBundle\Domain\Service\Budget\Dto\BudgetStructureParentElementDto;
use App\EconumoBundle\Domain\Service\Currency\CurrencyConvertorInterface;
use App\EconumoBundle\Domain\Service\Currency\Dto\CurrencyConvertorDto;

readonly class BudgetStructureBuilder
{
    public function __construct(
        private BudgetEnvelopeRepositoryInterface $budgetEnvelopeRepository,
        private BudgetFolderRepositoryInterface $budgetFolderRepository,
        private BudgetStructureFolderDtoAssembler $budgetStructureFolderDtoAssembler,
        private CurrencyConvertorInterface $currencyConvertor,
        private BudgetElementRepositoryInterface $budgetElementRepository,
    ) {
    }

    /**
     * @param BudgetElementBudgetedAmountDto[] $elementsBudgets
     * @param BudgetElementSpendingDto[] $elementsSpending
     */
    public function build(
        Budget $budget,
        BudgetFiltersDto $budgetFilters,
        array $elementsBudgets,
        array $elementsSpending,
    ): BudgetStructureDto {
        $elementsOptions = $this->getElementOptions($budget->getId());
        $folders = [];
        foreach ($this->getFolders($budget->getId()) as $folder) {
            $folders[] = $this->budgetStructureFolderDtoAssembler->assemble($folder);
        }

        $toConvert = [];
        $categoryUsed = [];
        $elements = [];
        $budgetCurrencyId = $budget->getCurrencyId();

        // Envelopes -->
        $envelopes = $this->getEnvelopes($budget->getId());
        foreach ($envelopes as $envelope) {
            $index = sprintf('%s-%s', $envelope->getId()->getValue(), BudgetElementType::ENVELOPE_ALIAS);
            $currencyId = ($elementsOptions[$index] ?? null)?->getCurrency()?->getId() ?? $budget->getCurrencyId();
            $folderId = ($elementsOptions[$index] ?? null)?->getFolder()?->getId();
            $position = ($elementsOptions[$index] ?? null)?->getPosition() ?? BudgetElement::POSITION_UNSET;
            $budgeted = $elementsBudgets[$index]?->budgeted ?? new DecimalNumber();
            $budgetedBefore = $elementsBudgets[$index]?->budgetedBefore ?? new DecimalNumber();
            $children = [];
            foreach ($envelope->getCategories() as $category) {
                if (array_key_exists($category->getId()->getValue(), $categoryUsed)) {
                    continue;
                }

                $subIndex = sprintf('%s-%s', $category->getId()->getValue(), BudgetElementType::CATEGORY_ALIAS);
                $children[$subIndex] = [
                    'id' => $category->getId(),
                    'type' => BudgetElementType::category(),
                    'name' => $category->getName(),
                    'icon' => $category->getIcon(),
                    'ownerId' => $category->getUserId(),
                    'isArchived' => $category->isArchived(),
                    'currenciesSpent' => ($elementsSpending[$subIndex] ?? null)?->spendingInCategories[$category->getId()->getValue()]->currenciesSpent ?? [],
                    'currenciesSpentBefore' => ($elementsSpending[$subIndex] ?? null)?->spendingInCategories[$category->getId()->getValue()]->currenciesSpentBefore ?? [],
                ];

                /** @var BudgetElementAmountSpentDto $spent */
                foreach ($children[$subIndex]['currenciesSpent'] as $spent) {
                    $toConvertSpent = new CurrencyConvertorDto(
                        $spent->periodStart,
                        $spent->periodEnd,
                        $spent->currencyId,
                        $currencyId,
                        $spent->amount
                    );
                    $toConvert[sprintf('%s_spent_%s', $index, $subIndex)][] = $toConvertSpent;
                    $toConvert[sprintf('spent_%s', $index)][] = $toConvertSpent;

                    $toConvertSpentBudget = new CurrencyConvertorDto(
                        $spent->periodStart,
                        $spent->periodEnd,
                        $spent->currencyId,
                        $budgetCurrencyId,
                        $spent->amount
                    );
                    $toConvert[sprintf('%s_spent-budget_%s', $index, $subIndex)][] = $toConvertSpentBudget;
                    $toConvert[sprintf('spent-budget_%s', $index)][] = $toConvertSpentBudget;
                }

                /** @var BudgetElementAmountSpentDto $spentBefore */
                foreach ($children[$subIndex]['currenciesSpentBefore'] as $spentBefore) {
                    $toConvertSpentBefore = new CurrencyConvertorDto(
                        $spentBefore->periodStart,
                        $spentBefore->periodEnd,
                        $spentBefore->currencyId,
                        $currencyId,
                        $spentBefore->amount
                    );
                    $toConvert[sprintf('%s_spent-before_%s', $index, $subIndex)][] = $toConvertSpentBefore;
                    $toConvert[sprintf('spent-before_%s', $index)][] = $toConvertSpentBefore;
                }

                $categoryUsed[$category->getId()->getValue()] = $category->getId()->getValue();
            }

            $item = [
                'id' => $envelope->getId(),
                'type' => BudgetElementType::envelope(),
                'name' => $envelope->getName(),
                'icon' => $envelope->getIcon(),
                'ownerId' => null,
                'currencyId' => $currencyId,
                'isArchived' => $envelope->isArchived(),
                'folderId' => $folderId,
                'position' => $position,
                'budgeted' => $budgeted,
                'budgetedBefore' => $budgetedBefore,
                'currenciesSpent' => [],
                'currenciesSpentBefore' => [],
                'children' => $children,
            ];
            if (!$envelope->isArchived() || !$budgeted->isZero() || !$budgetedBefore->isZero() || $children !== []) {
                $elements[$index] = $item;
            }
        }

        // Envelopes <--


        // Tags -->
        foreach ($budgetFilters->tags as $tag) {
            $index = sprintf('%s-%s', $tag->getId()->getValue(), BudgetElementType::TAG_ALIAS);
            $currencyId = ($elementsOptions[$index] ?? null)?->getCurrency()?->getId() ?? $budget->getCurrencyId();
            $folderId = ($elementsOptions[$index] ?? null)?->getFolder()?->getId();
            $position = ($elementsOptions[$index] ?? null)?->getPosition() ?? BudgetElement::POSITION_UNSET;
            $budgeted = $elementsBudgets[$index]?->budgeted ?? new DecimalNumber();
            $budgetedBefore = $elementsBudgets[$index]?->budgetedBefore ?? new DecimalNumber();
            $children = [];
            if (!array_key_exists($index, $elementsSpending)) {
                continue;
            }

            foreach ($elementsSpending[$index]->spendingInCategories as $categoryId => $categorySpending) {
                $category = $budgetFilters->categories[$categoryId];
                $subIndex = sprintf('%s-%s', $categoryId, BudgetElementType::CATEGORY_ALIAS);
                $children[$subIndex] = [
                    'id' => $categorySpending->categoryId,
                    'type' => BudgetElementType::category(),
                    'name' => $category->getName(),
                    'icon' => $category->getIcon(),
                    'ownerId' => $category->getUserId(),
                    'isArchived' => $category->isArchived(),
                    'currenciesSpent' => $categorySpending->currenciesSpent,
                    'currenciesSpentBefore' => $categorySpending->currenciesSpentBefore
                ];

                /** @var BudgetElementAmountSpentDto $spent */
                foreach ($children[$subIndex]['currenciesSpent'] as $spent) {
                    $toConvertSpent = new CurrencyConvertorDto(
                        $spent->periodStart,
                        $spent->periodEnd,
                        $spent->currencyId,
                        $currencyId,
                        $spent->amount
                    );
                    $toConvert[sprintf('%s_spent_%s', $index, $subIndex)][] = $toConvertSpent;
                    $toConvert[sprintf('spent_%s', $index)][] = $toConvertSpent;

                    $toConvertSpentBudget = new CurrencyConvertorDto(
                        $spent->periodStart,
                        $spent->periodEnd,
                        $spent->currencyId,
                        $budgetCurrencyId,
                        $spent->amount
                    );
                    $toConvert[sprintf('%s_spent-budget_%s', $index, $subIndex)][] = $toConvertSpentBudget;
                    $toConvert[sprintf('spent-budget_%s', $index)][] = $toConvertSpentBudget;
                }

                /** @var BudgetElementAmountSpentDto $spentBefore */
                foreach ($children[$subIndex]['currenciesSpentBefore'] as $spentBefore) {
                    $toConvertSpentBefore = new CurrencyConvertorDto(
                        $spentBefore->periodStart,
                        $spentBefore->periodEnd,
                        $spentBefore->currencyId,
                        $currencyId,
                        $spentBefore->amount
                    );
                    $toConvert[sprintf('%s_spent-before_%s', $index, $subIndex)][] = $toConvertSpentBefore;
                    $toConvert[sprintf('spent-before_%s', $index)][] = $toConvertSpentBefore;
                }
            }

            $item = [
                'id' => $tag->getId(),
                'type' => BudgetElementType::tag(),
                'name' => $tag->getName(),
                'icon' => $tag->getIcon(),
                'ownerId' => $tag->getUserId(),
                'currencyId' => $currencyId,
                'isArchived' => $tag->isArchived(),
                'folderId' => $folderId,
                'position' => $position,
                'budgeted' => $budgeted,
                'budgetedBefore' => $budgetedBefore,
                'currenciesSpent' => [],
                'currenciesSpentBefore' => [],
                'children' => $children
            ];
            if (!$tag->isArchived() || !$budgeted->isZero() || !$budgetedBefore->isZero() || $children !== []) {
                $elements[$index] = $item;
            }
        }

        // Tags <--


        // Categories -->
        foreach ($budgetFilters->categories as $category) {
            if ($category->getType()->isIncome()) {
                continue;
            }

            if (array_key_exists($category->getId()->getValue(), $categoryUsed)) {
                continue;
            }

            $type = BudgetElementType::category();
            $index = sprintf('%s-%s', $category->getId()->getValue(), $type->getAlias());
            $currencyId = ($elementsOptions[$index] ?? null)?->getCurrency()?->getId() ?? $budget->getCurrencyId();
            $folderId = ($elementsOptions[$index] ?? null)?->getFolder()?->getId();
            $position = ($elementsOptions[$index] ?? null)?->getPosition() ?? BudgetElement::POSITION_UNSET;
            $budgeted = $elementsBudgets[$index]?->budgeted ?? new DecimalNumber();
            $budgetedBefore = $elementsBudgets[$index]?->budgetedBefore ?? new DecimalNumber();
            $itemCategorySpending = null;
            if (array_key_exists($index, $elementsSpending)) {
                $itemCategorySpending = $elementsSpending[$index]->spendingInCategories[$category->getId()->getValue()] ?? null;
            }

            $currenciesSpent = $itemCategorySpending?->currenciesSpent ?? [];
            $currenciesSpentBefore = $itemCategorySpending?->currenciesSpentBefore ?? [];
            $item = [
                'id' => $category->getId(),
                'type' => $type,
                'name' => $category->getName(),
                'icon' => $category->getIcon(),
                'ownerId' => $category->getUserId(),
                'currencyId' => $currencyId,
                'isArchived' => $category->isArchived(),
                'folderId' => $folderId,
                'position' => $position,
                'budgeted' => $budgeted,
                'budgetedBefore' => $budgetedBefore,
                'currenciesSpent' => $currenciesSpent,
                'currenciesSpentBefore' => $currenciesSpentBefore,
                'children' => []
            ];
            if (!$category->isArchived() || count($currenciesSpent) || count($currenciesSpentBefore) || !$budgeted->isZero() || !$budgetedBefore->isZero()) {
                $elements[$index] = $item;
                foreach ($currenciesSpent as $spent) {
                    $toConvert[sprintf('spent_%s', $index)][] = new CurrencyConvertorDto(
                        $spent->periodStart,
                        $spent->periodEnd,
                        $spent->currencyId,
                        $currencyId,
                        $spent->amount
                    );
                    $toConvert[sprintf('spent-budget_%s', $index)][] = new CurrencyConvertorDto(
                        $spent->periodStart,
                        $spent->periodEnd,
                        $spent->currencyId,
                        $budgetCurrencyId,
                        $spent->amount
                    );
                }

                foreach ($currenciesSpentBefore as $spentBefore) {
                    $toConvert[sprintf('spent-before_%s', $index)][] = new CurrencyConvertorDto(
                        $spentBefore->periodStart,
                        $spentBefore->periodEnd,
                        $spentBefore->currencyId,
                        $currencyId,
                        $spentBefore->amount
                    );
                }
            }
        }

        // Categories <--

        $result = [];
        $amounts = $this->currencyConvertor->bulkConvert($budgetFilters->periodStart, $budgetFilters->periodEnd, $toConvert);
        foreach ($elements as $index => $element) {
            $spent = $amounts[sprintf('spent_%s', $index)] ?? new DecimalNumber();
            $spentBudget = $amounts[sprintf('spent-budget_%s', $index)] ?? new DecimalNumber();
            $spentBefore = $amounts[sprintf('spent-before_%s', $index)] ?? new DecimalNumber();
            $children = [];
            foreach ($element['children'] as $subIndex => $subElement) {
                $subElementSpent = $amounts[sprintf('%s_spent_%s', $index, $subIndex)] ?? new DecimalNumber();
                $subElementBudgetSpent = $amounts[sprintf('%s_spent-budget_%s', $index, $subIndex)] ?? new DecimalNumber();
//                $subElementSpentBefore = $amounts[sprintf('%s_spent-before_%s', $index, $subIndex)] ?? new DecimalNumber();
//                $spent = $spent->add($subElementSpent);
//                $spentBudget = $spentBudget->add($subElementBudgetSpent);
//                $spentBefore = $spentBefore->add($subElementSpentBefore);
                if (!$subElement['isArchived'] || !$subElementSpent->isZero()) {
                    if ($element['type']->isTag() && $subElementSpent->isZero()) {
                        continue;
                    }

                    $children[] = new BudgetStructureChildElementDto(
                        $subElement['id'],
                        $subElement['type'],
                        $subElement['name'],
                        $subElement['icon'],
                        $subElement['ownerId'],
                        $subElement['isArchived'],
                        $subElementSpent,
                        $subElementBudgetSpent,
                        $subElement['currenciesSpent']
                    );
                }
            }

            /** @var DecimalNumber $available */
            $available = $element['budgetedBefore']->sub($spentBefore);
            if ($element['isArchived'] && ($available->isZero() && $spent->isZero() && $element['budgeted']->isZero()) && (!$element['type']->isEnvelope() || ($element['type']->isEnvelope() && $children === []))) {
                continue;
            }

            $result[] = new BudgetStructureParentElementDto(
                $element['id'],
                $element['type'],
                $element['name'],
                $element['icon'],
                $element['ownerId'],
                $element['currencyId'],
                $element['isArchived'],
                $element['folderId'],
                $element['position'],
                $element['budgeted'],
                $available->sub($spent),
                $spent,
                $spentBudget,
                $element['currenciesSpent'],
                $children,
            );
        }

        usort($result, static fn(BudgetStructureParentElementDto $a, BudgetStructureParentElementDto $b): int => $a->position <=> $b->position);

        return new BudgetStructureDto($folders, $result);
    }

    /**
     * @return BudgetFolder[]
     */
    private function getFolders(Id $budgetId): array
    {
        $folders = $this->budgetFolderRepository->getByBudgetId($budgetId);

        return array_values($folders);
    }

    /**
     * @return BudgetEnvelope[]
     */
    private function getEnvelopes(Id $budgetId): array
    {
        return $this->budgetEnvelopeRepository->getByBudgetId($budgetId);
    }

    /**
     * @return BudgetElement[]
     */
    private function getElementOptions(Id $budgetId): array
    {
        $elementsOptions = $this->budgetElementRepository->getByBudgetId($budgetId);
        $elementsOptionsAssoc = [];
        foreach ($elementsOptions as $item) {
            $index = sprintf('%s-%s', $item->getExternalId()->getValue(), $item->getType()->getAlias());
            $elementsOptionsAssoc[$index] = $item;
        }

        return $elementsOptionsAssoc;
    }
}
