<?php

declare(strict_types=1);

namespace App\EconumoBundle\Application\Connection\Dto;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     required={"accountId", "userId"}
 * )
 */
class RevokeAccountAccessV1RequestDto
{
    /**
     * @OA\Property(example="0aaa0450-564e-411e-8018-7003f6dbeb92")
     */
    public string $accountId;

    /**
     * @OA\Property(example="aff21334-96f0-4fb1-84d8-0223d0280954")
     */
    public string $userId;
}
