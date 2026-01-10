<?php

declare(strict_types=1);

namespace App\EconumoBundle\Application\Connection\Dto;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     required={"id"}
 * )
 */
class DeleteConnectionV1RequestDto
{
    /**
     * User id
     * @OA\Property(example="77be9577-147b-4f05-9aa7-91d9b159de5b")
     */
    public string $id;
}
