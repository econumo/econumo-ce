<?php

declare(strict_types=1);

namespace App\EconumoBundle\Application\Connection\Dto;

use App\EconumoBundle\Application\Connection\Dto\ConnectionInviteResultDto;
use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     required={"item"}
 * )
 */
class GenerateInviteV1ResultDto
{
    /**
     * @OA\Property()
     */
    public ConnectionInviteResultDto $item;
}
