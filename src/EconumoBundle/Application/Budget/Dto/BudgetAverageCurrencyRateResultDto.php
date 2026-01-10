<?php

declare(strict_types=1);

namespace App\EconumoBundle\Application\Budget\Dto;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     required={"currencyId", "baseCurrencyId", "rate", "periodStart", "periodEnd"}
 * )
 */
class BudgetAverageCurrencyRateResultDto
{
    /**
     * @OA\Property(example="05c8f3e1-d77f-4b37-b2ca-0fc5f0f0c7a9")
     */
    public string $currencyId;

    /**
     * @OA\Property(example="05c8f3e1-d77f-4b37-b2ca-0fc5f0f0c7a9")
     */
    public string $baseCurrencyId;

    /**
     * @OA\Property(example="12.05")
     */
    public string $rate;

    /**
     * @OA\Property(example="2022-02-01")
     */
    public string $periodStart;

    /**
     * @OA\Property(example="2022-03-01")
     */
    public string $periodEnd;
}