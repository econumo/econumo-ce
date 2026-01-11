<?php

declare(strict_types=1);

namespace App\EconumoBundle\Application\Category\Assembler;

use App\EconumoBundle\Application\Category\Assembler\UserCategoryToDtoResultAssembler;
use App\EconumoBundle\Application\Category\Dto\GetCategoryListV1RequestDto;
use App\EconumoBundle\Application\Category\Dto\GetCategoryListV1ResultDto;
use App\EconumoBundle\Domain\Entity\Category;

class GetCategoryListV1ResultAssembler
{
    public function __construct(private readonly UserCategoryToDtoResultAssembler $categoryToDtoV1ResultAssembler)
    {
    }

    /**
     * @param Category[] $categories
     */
    public function assemble(
        GetCategoryListV1RequestDto $dto,
        array $categories
    ): GetCategoryListV1ResultDto {
        $result = new GetCategoryListV1ResultDto();
        $result->items = [];
        foreach ($categories as $category) {
            $result->items[] = $this->categoryToDtoV1ResultAssembler->assemble($category);
        }

        return $result;
    }
}
