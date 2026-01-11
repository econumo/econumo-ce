<?php

declare(strict_types=1);

namespace App\EconumoBundle\Application\Account\Dto;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     required={"id"}
 * )
 */
class HideFolderV1RequestDto
{
    /**
     * @OA\Property(example="1ad16d32-36af-496e-9867-3919436b8d86")
     */
    public string $id;
}
