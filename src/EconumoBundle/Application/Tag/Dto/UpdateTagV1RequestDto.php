<?php

declare(strict_types=1);

namespace App\EconumoBundle\Application\Tag\Dto;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     required={"id", "name"}
 * )
 */
class UpdateTagV1RequestDto
{
    /**
     * @OA\Property(example="4b53d029-c1ed-46ad-8d86-1049542f4a7e")
     */
    public string $id;

    /**
     * @OA\Property(example="#work")
     */
    public string $name;
}
