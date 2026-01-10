<?php

declare(strict_types=1);

namespace App\EconumoBundle\Application\Account\Dto;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     required={"id", "name", "currencyId", "balance", "icon"}
 * )
 */
class CreateAccountV1RequestDto
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
     * @OA\Property(example="fe5d9269-b69c-4841-9c04-136225447eca")
     */
    public string $currencyId;

    /**
     * @OA\Property(example="21007.64")
     */
    public float|string $balance = 0.0;

    /**
     * @OA\Property(example="wallet")
     */
    public string $icon = '';

    /**
     * @OA\Property(example="fe5d9269-b69c-4841-9c04-136225447eca")
     */
    public string $folderId;
}
