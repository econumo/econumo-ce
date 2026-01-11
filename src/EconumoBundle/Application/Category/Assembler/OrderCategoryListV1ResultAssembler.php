<?php

declare(strict_types=1);

namespace App\EconumoBundle\Application\Category\Assembler;

use App\EconumoBundle\Application\Category\Assembler\UserCategoryToDtoResultAssembler;
use App\EconumoBundle\Application\Category\Dto\OrderCategoryListV1RequestDto;
use App\EconumoBundle\Application\Category\Dto\OrderCategoryListV1ResultDto;
use App\EconumoBundle\Domain\Entity\ValueObject\Id;
use App\EconumoBundle\Domain\Repository\CategoryRepositoryInterface;

class OrderCategoryListV1ResultAssembler
{
    public function __construct(private readonly CategoryRepositoryInterface $categoryRepository, private readonly UserCategoryToDtoResultAssembler $categoryToDtoResultAssembler)
    {
    }

    public function assemble(
        OrderCategoryListV1RequestDto $dto,
        Id $userId
    ): OrderCategoryListV1ResultDto {
        $result = new OrderCategoryListV1ResultDto();
        $categories = $this->categoryRepository->findAvailableForUserId($userId);
        $result->items = [];
        foreach ($categories as $category) {
            $result->items[] = $this->categoryToDtoResultAssembler->assemble($category);
        }

        return $result;
    }
}
