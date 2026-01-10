<?php

declare(strict_types=1);

namespace App\EconumoBundle\Application\Category\Dto;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     required={"id"}
 * )
 */
class UnarchiveCategoryV1RequestDto
{
    /**
     * @OA\Property(example="95587d1d-2c39-4efc-98f3-23c755da44a4")
     */
    public string $id;
}
