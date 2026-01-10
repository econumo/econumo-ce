<?php

declare(strict_types=1);

namespace App\EconumoBundle\Application\Budget\Dto;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     required={"budgetId", "id", "name"}
 * )
 */
class UpdateBudgetFolderV1RequestDto
{
    /**
     * @OA\Property(example="9b29b760-ddca-46fb-a754-8743fc2c49a7")
     */
    public string $budgetId;

    /**
     * Folder ID
     * @OA\Property(example="9b29b760-ddca-46fb-a754-8743fc2c49a7")
     */
    public string $id;

    /**
     * @OA\Property(example="Savings")
     */
    public string $name;
}
