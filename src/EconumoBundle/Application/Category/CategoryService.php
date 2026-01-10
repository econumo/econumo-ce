<?php

declare(strict_types=1);

namespace App\EconumoBundle\Application\Category;

use App\EconumoBundle\Application\Category\Assembler\ArchiveCategoryV1ResultAssembler;
use App\EconumoBundle\Application\Category\Assembler\CreateCategoryV1ResultAssembler;
use App\EconumoBundle\Application\Category\Assembler\DeleteCategoryV1ResultAssembler;
use App\EconumoBundle\Application\Category\Assembler\UpdateCategoryV1ResultAssembler;
use App\EconumoBundle\Application\Category\Dto\ArchiveCategoryV1RequestDto;
use App\EconumoBundle\Application\Category\Dto\ArchiveCategoryV1ResultDto;
use App\EconumoBundle\Application\Category\Dto\CreateCategoryV1RequestDto;
use App\EconumoBundle\Application\Category\Dto\CreateCategoryV1ResultDto;
use App\EconumoBundle\Application\Category\Dto\DeleteCategoryV1RequestDto;
use App\EconumoBundle\Application\Category\Dto\DeleteCategoryV1ResultDto;
use App\EconumoBundle\Application\Category\Dto\UnarchiveCategoryV1RequestDto;
use App\EconumoBundle\Application\Category\Dto\UnarchiveCategoryV1ResultDto;
use App\EconumoBundle\Application\Category\Assembler\UnarchiveCategoryV1ResultAssembler;
use App\EconumoBundle\Application\Category\Dto\UpdateCategoryV1RequestDto;
use App\EconumoBundle\Application\Category\Dto\UpdateCategoryV1ResultDto;
use App\EconumoBundle\Application\Exception\AccessDeniedException;
use App\EconumoBundle\Application\Exception\ValidationException;
use App\EconumoBundle\Domain\Entity\ValueObject\CategoryName;
use App\EconumoBundle\Domain\Entity\ValueObject\CategoryType;
use App\EconumoBundle\Domain\Entity\ValueObject\Icon;
use App\EconumoBundle\Domain\Entity\ValueObject\Id;
use App\EconumoBundle\Domain\Repository\CategoryRepositoryInterface;
use App\EconumoBundle\Domain\Service\AccountAccessServiceInterface;
use App\EconumoBundle\Domain\Service\CategoryServiceInterface;
use App\EconumoBundle\Domain\Service\Translation\TranslationServiceInterface;

class CategoryService
{
    public function __construct(private readonly CreateCategoryV1ResultAssembler $createCategoryV1ResultAssembler, private readonly CategoryServiceInterface $categoryService, private readonly AccountAccessServiceInterface $accountAccessService, private readonly DeleteCategoryV1ResultAssembler $deleteCategoryV1ResultAssembler, private readonly CategoryRepositoryInterface $categoryRepository, private readonly UpdateCategoryV1ResultAssembler $updateCategoryV1ResultAssembler, private readonly ArchiveCategoryV1ResultAssembler $archiveCategoryV1ResultAssembler, private readonly UnarchiveCategoryV1ResultAssembler $unarchiveCategoryV1ResultAssembler, private readonly TranslationServiceInterface $translationService)
    {
    }

    public function createCategory(
        CreateCategoryV1RequestDto $dto,
        Id $userId
    ): CreateCategoryV1ResultDto {
        $icon = new Icon(empty($dto->icon) ? 'local_offer' : $dto->icon);
        if ($dto->accountId !== null) {
            $accountId = new Id($dto->accountId);
            $this->accountAccessService->checkAddCategory($userId, $accountId);
            $category = $this->categoryService->createCategoryForAccount(
                $userId,
                $accountId,
                new CategoryName($dto->name),
                CategoryType::createFromAlias($dto->type),
                $icon
            );
        } else {
            $category = $this->categoryService->createCategory(
                $userId,
                new CategoryName($dto->name),
                CategoryType::createFromAlias($dto->type),
                $icon
            );
        }

        return $this->createCategoryV1ResultAssembler->assemble($dto, $category);
    }

    public function deleteCategory(
        DeleteCategoryV1RequestDto $dto,
        Id $userId
    ): DeleteCategoryV1ResultDto {
        $categoryId = new Id($dto->id);
        $category = $this->categoryRepository->get($categoryId);
        if (!$category->getUserId()->isEqual($userId)) {
            throw new ValidationException($this->translationService->trans('category.category.not_found'));
        }

        if ($dto->mode === $dto::MODE_DELETE) {
            $this->categoryService->deleteCategory($categoryId);
        } elseif ($dto->mode === $dto::MODE_REPLACE) {
            $this->categoryService->replaceCategory($categoryId, new Id($dto->replaceId));
        } else {
            throw new ValidationException($this->translationService->trans('category.category.action.unknown_action'));
        }

        return $this->deleteCategoryV1ResultAssembler->assemble($dto);
    }

    public function updateCategory(
        UpdateCategoryV1RequestDto $dto,
        Id $userId
    ): UpdateCategoryV1ResultDto {
        $categoryId = new Id($dto->id);
        $category = $this->categoryRepository->get($categoryId);
        if (!$category->getUserId()->isEqual($userId)) {
            throw new AccessDeniedException();
        }

        $this->categoryService->update($categoryId, new CategoryName($dto->name), new Icon($dto->icon));
        return $this->updateCategoryV1ResultAssembler->assemble($dto);
    }

    public function archiveCategory(
        ArchiveCategoryV1RequestDto $dto,
        Id $userId
    ): ArchiveCategoryV1ResultDto {
        $categoryId = new Id($dto->id);
        $category = $this->categoryRepository->get($categoryId);
        if (!$category->getUserId()->isEqual($userId)) {
            throw new AccessDeniedException();
        }

        $this->categoryService->archive($categoryId);
        return $this->archiveCategoryV1ResultAssembler->assemble($dto);
    }

    public function unarchiveCategory(
        UnarchiveCategoryV1RequestDto $dto,
        Id $userId
    ): UnarchiveCategoryV1ResultDto {
        $categoryId = new Id($dto->id);
        $category = $this->categoryRepository->get($categoryId);
        if (!$category->getUserId()->isEqual($userId)) {
            throw new AccessDeniedException();
        }

        $this->categoryService->unarchive($categoryId);
        return $this->unarchiveCategoryV1ResultAssembler->assemble($dto);
    }
}
