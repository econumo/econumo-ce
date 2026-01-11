<?php

declare(strict_types=1);

namespace App\EconumoBundle\Application\Budget\Dto;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     required={"budgetId", "elementId", "currencyId"}
 * )
 */
class ChangeElementCurrencyV1RequestDto
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
     * @OA\Property(example="fe5d9269-b69c-4841-9c04-136225447eca")
     */
    public string $currencyId;
}
