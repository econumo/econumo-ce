<?php

declare(strict_types=1);

namespace App\EconumoBundle\Application\Category\Assembler;

use App\EconumoBundle\Application\Category\Dto\UpdateCategoryV1RequestDto;
use App\EconumoBundle\Application\Category\Dto\UpdateCategoryV1ResultDto;

class UpdateCategoryV1ResultAssembler
{
    public function assemble(
        UpdateCategoryV1RequestDto $dto
    ): UpdateCategoryV1ResultDto {
        return new UpdateCategoryV1ResultDto();
    }
}
