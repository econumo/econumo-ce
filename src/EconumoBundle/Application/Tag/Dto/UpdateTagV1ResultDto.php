<?php

declare(strict_types=1);

namespace App\EconumoBundle\Application\Tag\Dto;

use App\EconumoBundle\Application\Tag\Dto\TagResultDto;
use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     required={"item"}
 * )
 */
class UpdateTagV1ResultDto
{
    /**
     * Tag
     * @OA\Property()
     */
    public TagResultDto $item;
}
