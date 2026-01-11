<?php

declare(strict_types=1);

namespace App\EconumoBundle\Application\Budget\Dto;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     required={"currencyId", "startBalance", "endBalance", "income", "expenses", "exchanges", "holdings"}
 * )
 */
class BudgetCurrencyBalanceResultDto
{
    /**
     * @OA\Property(example="05c8f3e1-d77f-4b37-b2ca-0fc5f0f0c7a9")
     */
    public string $currencyId;

    /**
     * @OA\Property(example="12.05")
     */
    public ?string $startBalance = null;

    /**
     * @OA\Property(example="12.05")
     */
    public ?string $endBalance = null;

    /**
     * @OA\Property(example="12.05")
     */
    public ?string $income = null;

    /**
     * @OA\Property(example="12.05")
     */
    public ?string $expenses = null;

    /**
     * @OA\Property(example="12.05")
     */
    public ?string $exchanges = null;

    /**
     * @OA\Property(example="12.05")
     */
    public ?string $holdings = null;
}