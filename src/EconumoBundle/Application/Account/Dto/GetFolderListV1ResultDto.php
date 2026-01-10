<?php

declare(strict_types=1);

namespace App\EconumoBundle\Application\Account\Dto;

use App\EconumoBundle\Application\Account\Dto\FolderResultDto;
use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     required={"items"}
 * )
 */
class GetFolderListV1ResultDto
{
    /**
     * @var FolderResultDto[]
     * @OA\Property()
     */
    public array $items = [];
}
