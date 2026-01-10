<?php

declare(strict_types=1);

namespace App\EconumoBundle\Application\Account\Dto;

use App\EconumoBundle\Application\Account\Dto\AccountResultDto;
use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     required={"items"}
 * )
 */
class GetAccountListV1ResultDto
{
    /**
     * @var AccountResultDto[]
     * @OA\Property()
     */
    public array $items = [];
}
