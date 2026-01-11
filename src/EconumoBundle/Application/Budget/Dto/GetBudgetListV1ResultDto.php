<?php

declare(strict_types=1);

namespace App\EconumoBundle\Application\Budget\Dto;

use App\EconumoBundle\Application\Budget\Dto\BudgetMetaResultDto;
use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     required={"items"}
 * )
 */
class GetBudgetListV1ResultDto
{
    /**
     * @var BudgetMetaResultDto[]
     * @OA\Property()
     */
    public array $items = [];
}
