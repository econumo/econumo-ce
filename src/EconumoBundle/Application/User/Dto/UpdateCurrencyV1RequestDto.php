<?php

declare(strict_types=1);

namespace App\EconumoBundle\Application\User\Dto;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     required={"currency"}
 * )
 */
class UpdateCurrencyV1RequestDto
{
    /**
     * @OA\Property(example="USD")
     */
    public string $currency;
}
