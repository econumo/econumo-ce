<?php

declare(strict_types=1);

namespace App\EconumoBundle\Application\System\Dto;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     required={"code", "rate"}
 * )
 */
class CurrencyRateRequestDto
{
    /**
     * @OA\Property(example="EUR")
     */
    public string $code;

    /**
     * @OA\Property(example="1.01")
     */
    public float|string $rate;
}
