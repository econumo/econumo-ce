<?php

declare(strict_types=1);

namespace App\EconumoBundle\Application\Connection\Dto;

use App\EconumoBundle\Application\Connection\Dto\ConnectionResultDto;
use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     required={"items"}
 * )
 */
class GetConnectionListV1ResultDto
{
    /**
     * @var ConnectionResultDto[]
     * @OA\Property()
     */
    public array $items = [];
}
