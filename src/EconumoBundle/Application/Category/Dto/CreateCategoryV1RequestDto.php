<?php

declare(strict_types=1);

namespace App\EconumoBundle\Application\Category\Dto;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     required={"id", "name", "type"}
 * )
 */
class CreateCategoryV1RequestDto
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
     * @OA\Property(example="expense")
     */
    public string $type;

    /**
     * @OA\Property(example="0aaa0450-564e-411e-8018-7003f6dbeb92")
     */
    public ?string $accountId = null;

    /**
     * @OA\Property(example="local_offer")
     */
    public ?string $icon = null;
}
