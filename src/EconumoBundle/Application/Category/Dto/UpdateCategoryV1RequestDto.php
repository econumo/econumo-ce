<?php

declare(strict_types=1);

namespace App\EconumoBundle\Application\Category\Dto;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     required={"id", "name", "icon"}
 * )
 */
class UpdateCategoryV1RequestDto
{
    /**
     * @OA\Property(example="3a2c32a4-45ec-4cb0-9794-a6bef87ba9a4")
     */
    public string $id;

    /**
     * @OA\Property(example="Food")
     */
    public string $name;

    /**
     * @OA\Property(example="local_offer")
     */
    public string $icon;
}
