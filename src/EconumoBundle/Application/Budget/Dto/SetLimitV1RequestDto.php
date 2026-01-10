<?php

declare(strict_types=1);

namespace App\EconumoBundle\Application\Budget\Dto;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     required={"budgetId", "elementId", "amount"}
 * )
 */
class SetLimitV1RequestDto
{
    /**
     * @OA\Property(example="0aaa0450-564e-411e-8018-7003f6dbeb92")
     */
    public string $budgetId;

    /**
     * @OA\Property(example="0aaa0450-564e-411e-8018-7003f6dbeb92")
     */
    public string $elementId;

    /**
     * @OA\Property(example="2022-02-01")
     */
    public string $period;

    /**
     * @OA\Property(example="100.1")
     */
    public float|int|string|null $amount = null;
}
