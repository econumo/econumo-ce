<?php

declare(strict_types=1);

namespace App\EconumoBundle\Application\Category\Assembler;

use App\EconumoBundle\Application\Category\Dto\DeleteCategoryV1RequestDto;
use App\EconumoBundle\Application\Category\Dto\DeleteCategoryV1ResultDto;

class DeleteCategoryV1ResultAssembler
{
    public function assemble(
        DeleteCategoryV1RequestDto $dto
    ): DeleteCategoryV1ResultDto {
        return new DeleteCategoryV1ResultDto();
    }
}
