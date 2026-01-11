<?php

declare(strict_types=1);

namespace App\EconumoBundle\Application\Currency\Dto;

use App\EconumoBundle\Application\Currency\Dto\CurrencyResultDto;
use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     required={"items"}
 * )
 */
class GetCurrencyListV1ResultDto
{
    /**
     * @var CurrencyResultDto[]
     * @OA\Property()
     */
    public array $items = [];
}
