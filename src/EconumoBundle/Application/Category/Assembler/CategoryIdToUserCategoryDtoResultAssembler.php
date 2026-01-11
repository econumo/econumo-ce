<?php

declare(strict_types=1);

namespace App\EconumoBundle\Application\Category\Assembler;

use App\EconumoBundle\Application\Category\Assembler\UserCategoryToDtoResultAssembler;
use App\EconumoBundle\Application\Category\Dto\CategoryResultDto;
use App\EconumoBundle\Domain\Entity\ValueObject\Id;
use App\EconumoBundle\Domain\Repository\CategoryRepositoryInterface;

class CategoryIdToUserCategoryDtoResultAssembler
{
    public function __construct(private readonly CategoryRepositoryInterface $categoryRepository, private readonly UserCategoryToDtoResultAssembler $categoryToDtoResultAssembler)
    {
    }

    public function assemble(Id $categoryId): CategoryResultDto
    {
        $category = $this->categoryRepository->get($categoryId);
        return $this->categoryToDtoResultAssembler->assemble($category);
    }
}
