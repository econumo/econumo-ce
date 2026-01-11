<?php
declare(strict_types=1);

namespace App\EconumoBundle\Application\Budget\Dto;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     required={"id", "type", "name", "icon", "isArchived", "spent", "budgetSpent", "ownerUserId"}
 * )
 */
class BudgetStructureChildElementResultDto
{
    /**
     * @OA\Property(example="05c8f3e1-d77f-4b37-b2ca-0fc5f0f0c7a9")
     */
    public string $id;

    /**
     * @OA\Property(example="0")
     */
    public int $type;

    /**
     * @OA\Property(example="Car")
     */
    public string $name;

    /**
     * @OA\Property(example="Wallet")
     */
    public string $icon;

    /**
     * @OA\Property(example="0")
     */
    public int $isArchived;

    /**
     * @OA\Property(example="10.0")
     */
    public string $spent;

    /**
     * @OA\Property(example="10.0")
     */
    public string $budgetSpent;

    /**
     * @OA\Property(example="05c8f3e1-d77f-4b37-b2ca-0fc5f0f0c7a9")
     */
    public string $ownerUserId;
}