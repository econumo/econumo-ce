<?php

declare(strict_types=1);

namespace App\EconumoBundle\Application\Tag\Dto;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     required={"id"}
 * )
 */
class DeleteTagV1RequestDto
{
    /**
     * @OA\Property(example="4b53d029-c1ed-46ad-8d86-1049542f4a7e")
     */
    public string $id;
}
