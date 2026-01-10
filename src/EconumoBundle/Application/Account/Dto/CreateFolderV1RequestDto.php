<?php

declare(strict_types=1);

namespace App\EconumoBundle\Application\Account\Dto;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     required={"name"}
 * )
 */
class CreateFolderV1RequestDto
{
    /**
     * @OA\Property(example="Savings")
     */
    public string $name;
}
