<?php

declare(strict_types=1);

namespace App\EconumoBundle\Application\Budget\Dto;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     required={"budgetId", "id", "icon", "name", "currencyId", "categories"}
 * )
 */
class CreateEnvelopeV1RequestDto
{
    /**
     * @OA\Property(example="0aaa0450-564e-411e-8018-7003f6dbeb92")
     */
    public string $budgetId;

    /**
     * @OA\Property(example="0aaa0450-564e-411e-8018-7003f6dbeb92")
     */
    public string $id;

    /**
     * @OA\Property(example="fork")
     */
    public string $icon;

    /**
     * @OA\Property(example="Food")
     */
    public string $name;

    /**
     * @OA\Property(example="fe5d9269-b69c-4841-9c04-136225447eca")
     */
    public string $currencyId;

    /**
     * @OA\Property(type="array", @OA\Items(type="string"))
     */
    public array $categories = [];

    /**
     * @OA\Property(example="fe5d9269-b69c-4841-9c04-136225447eca")
     */
    public ?string $folderId = null;
}
