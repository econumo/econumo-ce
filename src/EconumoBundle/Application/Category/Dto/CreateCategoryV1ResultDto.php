<?php

declare(strict_types=1);

namespace App\EconumoBundle\Application\Category\Dto;

use App\EconumoBundle\Application\Category\Dto\CategoryResultDto;
use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     required={"item"}
 * )
 */
class CreateCategoryV1ResultDto
{
    /**
     * Category
     * @OA\Property()
     */
    public CategoryResultDto $item;
}
