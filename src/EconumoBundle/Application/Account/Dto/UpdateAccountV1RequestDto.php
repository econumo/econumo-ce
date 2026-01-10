<?php

declare(strict_types=1);

namespace App\EconumoBundle\Application\Account\Dto;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     required={"id", "name", "balance", "icon", "updatedAt"}
 * )
 */
class UpdateAccountV1RequestDto
{
    /**
     * @OA\Property(example="0aaa0450-564e-411e-8018-7003f6dbeb92")
     */
    public string $id;

    /**
     * @OA\Property(example="Cash")
     */
    public string $name;

    /**
     * @OA\Property(example="21007.64")
     */
    public float|string $balance = 0.0;

    /**
     * @OA\Property(example="wallet")
     */
    public string $icon;

    /**
     * @OA\Property(example="a3b8e8d7-9f0a-4e51-8b7c-2a8b5f5f5b3f")
     */
    public ?string $currencyId = null;

    /**
     * @OA\Property(example="2020-01-01 23:59:59")
     */
    public string $updatedAt;
}
