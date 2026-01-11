<?php

declare(strict_types=1);

namespace App\EconumoBundle\Application\System\Dto;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     required={"id"}
 * )
 */
class CreateUserV1ResultDto
{
    /**
     * @OA\Property(example="2acbfdcf-ff94-4c07-9819-7d3f1b8d20ce")
     */
    public string $id;
}
