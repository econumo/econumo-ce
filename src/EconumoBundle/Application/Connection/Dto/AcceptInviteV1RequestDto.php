<?php

declare(strict_types=1);

namespace App\EconumoBundle\Application\Connection\Dto;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     required={"code"}
 * )
 */
class AcceptInviteV1RequestDto
{
    /**
     * @OA\Property(example="2b345")
     */
    public string $code;
}
