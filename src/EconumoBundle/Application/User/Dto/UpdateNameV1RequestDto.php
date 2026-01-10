<?php

declare(strict_types=1);

namespace App\EconumoBundle\Application\User\Dto;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     required={"name"}
 * )
 */
class UpdateNameV1RequestDto
{
    /**
     * @OA\Property(example="John")
     */
    public string $name;
}
