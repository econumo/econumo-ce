<?php

declare(strict_types=1);


namespace App\EconumoBundle\Application\Connection\Dto;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     required={"id", "role"}
 * )
 */
class AccountAccessResultDto
{
    /**
     * Account id
     * @var string
     * @OA\Property(example="0aaa0450-564e-411e-8018-7003f6dbeb92")
     */
    public string $id;

    /**
     * @var string
     * @OA\Property(example="77be9577-147b-4f05-9aa7-91d9b159de5b")
     */
    public string $ownerUserId;

    /**
     * User role
     * @var string
     * @OA\Property(example="admin")
     */
    public string $role;
}
