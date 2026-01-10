<?php

declare(strict_types=1);

namespace App\EconumoBundle\Application\Tag\Dto;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     required={"id", "name"}
 * )
 */
class CreateTagV1RequestDto
{
    /**
     * @OA\Property(example="123")
     */
    public string $id;

    /**
     * @OA\Property(example="#shopping")
     */
    public string $name;

    /**
     * @OA\Property(example="0aaa0450-564e-411e-8018-7003f6dbeb92")
     */
    public ?string $accountId = null;
}
