<?php

declare(strict_types=1);

namespace App\EconumoBundle\Application\Budget\Dto;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     required={"items"}
 * )
 */
class GetBudgetTransactionListV1ResultDto
{
    /**
     * @var BudgetTransactionResultDto[]
     * @OA\Property()
     */
    public array $items = [];
}
