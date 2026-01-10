<?php

declare(strict_types=1);

namespace App\EconumoBundle\Application\Budget\Dto;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     required={"budgetId", "id"}
 * )
 */
class DeleteFolderV1RequestDto
{
    /**
     * @OA\Property(example="9b29b760-ddca-46fb-a754-8743fc2c49a7")
     */
    public string $budgetId;

    /**
     * @OA\Property(example="9b29b760-ddca-46fb-a754-8743fc2c49a7")
     */
    public string $id;
}
