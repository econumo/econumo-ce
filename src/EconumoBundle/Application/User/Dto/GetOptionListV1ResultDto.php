<?php

declare(strict_types=1);

namespace App\EconumoBundle\Application\User\Dto;

use App\EconumoBundle\Application\User\Dto\OptionResultDto;
use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     required={"items"}
 * )
 */
class GetOptionListV1ResultDto
{
    /**
     * @var OptionResultDto[]
     * @OA\Property()
     */
    public array $items = [];
}
