<?php
declare(strict_types=1);

namespace App\EconumoBundle\Application\Budget\Dto;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     required={"id", "name", "position"}
 * )
 */
class BudgetFolderResultDto
{
    /**
     * @OA\Property(example="05c8f3e1-d77f-4b37-b2ca-0fc5f0f0c7a9")
     */
    public string $id;

    /**
     * @OA\Property(example="Expenses")
     */
    public string $name;

    /**
     * @OA\Property(example="0")
     */
    public int $position;
}