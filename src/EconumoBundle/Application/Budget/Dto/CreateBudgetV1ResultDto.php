<?php

declare(strict_types=1);

namespace App\EconumoBundle\Application\Budget\Dto;

use App\EconumoBundle\Application\Budget\Dto\BudgetResultDto;
use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     required={"item"}
 * )
 */
class CreateBudgetV1ResultDto
{
    /**
     * @OA\Property()
     */
    public BudgetResultDto $item;
}
