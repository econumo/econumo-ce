<?php

declare(strict_types=1);

namespace App\EconumoBundle\Application\Budget\Dto;

use App\EconumoBundle\Application\Budget\Dto\BudgetAccessResultDto;
use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     required={"id", "ownerUserId", "name", "startedAt", "currencyId", "access"}
 * )
 */
class BudgetMetaResultDto
{
    /**
     * @OA\Property(example="05c8f3e1-d77f-4b37-b2ca-0fc5f0f0c7a9")
     */
    public string $id;

    /**
     * Owner user id
     * @OA\Property(example="aff21334-96f0-4fb1-84d8-0223d0280954")
     */
    public string $ownerUserId;

    /**
     * @OA\Property(example="Family budget")
     */
    public string $name;

    /**
     * Budget start date
     * @var string
     * @OA\Property(example="2021-01-01 12:15:00")
     */
    public string $startedAt;

    /**
     * Budget currency ID
     * @OA\Property(example="aff21334-96f0-4fb1-84d8-0223d0280954")
     */
    public string $currencyId;

    /**
     * Account access
     * @var BudgetAccessResultDto[]
     * @OA\Property()
     */
    public array $access = [];
}