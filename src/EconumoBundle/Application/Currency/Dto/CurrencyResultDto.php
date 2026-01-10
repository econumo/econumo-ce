<?php

declare(strict_types=1);


namespace App\EconumoBundle\Application\Currency\Dto;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     required={"id", "code", "name", "symbol", "fractionDigits"}
 * )
 */
class CurrencyResultDto
{
    /**
     * Id
     * @var string
     * @OA\Property(example="77adad8a-9982-4e08-8fd7-5ef336c7a5c9")
     */
    public string $id;

    /**
     * Currency code
     * @var string
     * @OA\Property(example="USD")
     */
    public string $code;

    /**
     * Currency
     * @var string
     * @OA\Property(example="United States Dollar")
     */
    public string $name;

    /**
     * Currency symbol
     * @var string
     * @OA\Property(example="$")
     */
    public string $symbol;

    /**
     * Currency's fraction digits
     * @OA\Property(example="2")
     */
    public int $fractionDigits;
}
