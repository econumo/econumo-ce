<?php

declare(strict_types=1);


namespace App\EconumoBundle\Application\Currency\Dto;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     required={"currencyId", "baseCurrencyId", "rate", "updatedAt"}
 * )
 */
class CurrencyRateResultDto
{
    /**
     * Currency id
     * @var string
     * @OA\Property(example="77adad8a-9982-4e08-8fd7-5ef336c7a5c9")
     */
    public string $currencyId;

    /**
     * Base currency id
     * @var string
     * @OA\Property(example="77adad8a-9982-4e08-8fd7-5ef336c7a5c9")
     */
    public string $baseCurrencyId;

    /**
     * Currency rate
     * @OA\Property(example="0.123")
     */
    public string $rate;

    /**
     * Updated at
     * @var string
     * @OA\Property(example="2021-01-01 12:15:00")
     */
    public string $updatedAt;
}
