<?php

declare(strict_types=1);

namespace App\EconumoBundle\Application\Budget\Dto;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     required={"items"}
 * )
 */
class AcceptAccessV1ResultDto
{
    /**
     * @var BudgetMetaResultDto[]
     * @OA\Property()
     */
    public array $items = [];
}
