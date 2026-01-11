<?php

declare(strict_types=1);

namespace App\EconumoBundle\Application\Budget\Dto;

use App\EconumoBundle\Application\Budget\Dto\BudgetMetaResultDto;
use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     required={"item"}
 * )
 */
class UpdateBudgetV1ResultDto
{
    /**
     * @OA\Property()
     */
    public BudgetMetaResultDto $item;
}
