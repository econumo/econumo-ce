<?php

declare(strict_types=1);

namespace App\EconumoBundle\Application\Budget\Dto;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     required={"item"}
 * )
 */
class CreateBudgetFolderV1ResultDto
{
    /**
     * @OA\Property()
     */
    public BudgetFolderResultDto $item;
}
