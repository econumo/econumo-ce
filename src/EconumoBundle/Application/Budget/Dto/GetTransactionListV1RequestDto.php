<?php

declare(strict_types=1);

namespace App\EconumoBundle\Application\Budget\Dto;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     required={"budgetId", "period", "categoryId", "tagId", "envelopeId"}
 * )
 */
class GetTransactionListV1RequestDto
{
    /**
     * @OA\Property(example="95587d1d-2c39-4efc-98f3-23c755da44a4")
     */
    public string $budgetId;

    /**
     * @OA\Property(example="2022-02-01")
     */
    public string $periodStart;

    /**
     * @OA\Property(example="95587d1d-2c39-4efc-98f3-23c755da44a4")
     */
    public ?string $categoryId = null;

    /**
     * @OA\Property(example="95587d1d-2c39-4efc-98f3-23c755da44a4")
     */
    public ?string $tagId = null;

    /**
     * @OA\Property(example="95587d1d-2c39-4efc-98f3-23c755da44a4")
     */
    public ?string $envelopeId = null;
}
