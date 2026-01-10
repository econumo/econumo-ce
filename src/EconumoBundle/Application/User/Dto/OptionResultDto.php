<?php
declare(strict_types=1);

namespace App\EconumoBundle\Application\User\Dto;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     required={"name", "value"}
 * )
 */
class OptionResultDto
{
    /**
     * Option name
     * @var string
     * @OA\Property(example="currency")
     */
    public string $name;

    /**
     * Option value
     * @var string|null
     * @OA\Property(example="USD")
     */
    public ?string $value = null;
}
