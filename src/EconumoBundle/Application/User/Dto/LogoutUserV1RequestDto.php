<?php

declare(strict_types=1);

namespace App\EconumoBundle\Application\User\Dto;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     required={"id"}
 * )
 */
class LogoutUserV1RequestDto
{
    /**
     * @OA\Property(example="123")
     */
    public string $id;
}
