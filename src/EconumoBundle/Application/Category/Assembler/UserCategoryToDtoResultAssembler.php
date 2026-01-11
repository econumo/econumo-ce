<?php

declare(strict_types=1);

namespace App\EconumoBundle\Application\Category\Assembler;

use App\EconumoBundle\Application\Category\Dto\CategoryResultDto;
use App\EconumoBundle\Domain\Entity\Category;

class UserCategoryToDtoResultAssembler
{
    public function assemble(Category $category): CategoryResultDto
    {
        $item = new CategoryResultDto();
        $item->id = $category->getId()->getValue();
        $item->ownerUserId = $category->getUserId()->getValue();
        $item->name = $category->getName()->getValue();
        $item->position = $category->getPosition();
        $item->type = $category->getType()->getAlias();
        $item->icon = $category->getIcon()->getValue();
        $item->isArchived = $category->isArchived() ? 1 : 0;
        $item->createdAt = $category->getCreatedAt()->format('Y-m-d H:i:s');
        $item->updatedAt = $category->getUpdatedAt()->format('Y-m-d H:i:s');
        return $item;
    }
}
