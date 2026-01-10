<?php

declare(strict_types=1);

namespace App\EconumoBundle\Application\Budget\Dto;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     required={"budgetId", "items"}
 * )
 */
class OrderBudgetFolderListV1RequestDto
{
    /**
     * @OA\Property(example="05c8f3e1-d77f-4b37-b2ca-0fc5f0f0c7a9")
     */
    public string $budgetId;

    /**
     * @var OrderBudgetFolderListItemV1RequestDto[]
     * @OA\Property()
     */
    public array $items = [];
}
