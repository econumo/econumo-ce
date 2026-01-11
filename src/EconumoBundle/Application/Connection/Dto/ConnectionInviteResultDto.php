<?php

declare(strict_types=1);


namespace App\EconumoBundle\Application\Connection\Dto;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     required={"code", "expiredAt"}
 * )
 */
class ConnectionInviteResultDto
{
    /**
     * Code
     * @var string
     * @OA\Property(example="2b855")
     */
    public string $code;

    /**
     * Expired at
     * @var string
     * @OA\Property(example="2021-01-01 12:15:00")
     */
    public string $expiredAt;
}
