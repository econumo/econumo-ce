<?php

declare(strict_types=1);

namespace App\EconumoBundle\Application\Category\Assembler;

use App\EconumoBundle\Application\Category\Assembler\UserCategoryToDtoResultAssembler;
use App\EconumoBundle\Application\Category\Dto\CreateCategoryV1RequestDto;
use App\EconumoBundle\Application\Category\Dto\CreateCategoryV1ResultDto;
use App\EconumoBundle\Domain\Entity\Category;

class CreateCategoryV1ResultAssembler
{
    public function __construct(private readonly UserCategoryToDtoResultAssembler $categoryToDtoV1ResultAssembler)
    {
    }

    public function assemble(
        CreateCategoryV1RequestDto $dto,
        Category $category
    ): CreateCategoryV1ResultDto {
        $result = new CreateCategoryV1ResultDto();
        $result->item = $this->categoryToDtoV1ResultAssembler->assemble($category);

        return $result;
    }
}
