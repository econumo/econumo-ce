<?php

declare(strict_types=1);

namespace App\EconumoBundle\Application\Transaction\Dto;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     required={}
 * )
 */
class GetTransactionListV1RequestDto
{
    /**
     * @OA\Property(example="0aaa0450-564e-411e-8018-7003f6dbeb92")
     */
    public ?string $accountId = null;

    /**
     * @OA\Property(example="2023-01-01 00:00:00")
     */
    public ?string $periodStart = null;

    /**
     * @OA\Property(example="2024-01-01 00:00:00")
     */
    public ?string $periodEnd = null;
}
