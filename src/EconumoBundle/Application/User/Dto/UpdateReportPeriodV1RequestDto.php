<?php

declare(strict_types=1);

namespace App\EconumoBundle\Application\User\Dto;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     required={"value"}
 * )
 */
class UpdateReportPeriodV1RequestDto
{
    /**
     * @OA\Property(example="monthly")
     */
    public string $value;
}
