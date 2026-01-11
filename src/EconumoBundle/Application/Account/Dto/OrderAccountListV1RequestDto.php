<?php

declare(strict_types=1);

namespace App\EconumoBundle\Application\Account\Dto;

use App\EconumoBundle\Domain\Service\Dto\AccountPositionDto;
use OpenApi\Annotations as OA;
use Nelmio\ApiDocBundle\Annotation\Model;

/**
 * @OA\Schema(
 *     required={"changes"}
 * )
 */
class OrderAccountListV1RequestDto
{
    /**
     * @var AccountPositionDto[]
     * @OA\Property(type="array", @OA\Items(type="object", ref=@Model(type=\App\EconumoBundle\Domain\Service\Dto\AccountPositionDto::class)))
     */
    public array $changes = [];
}
