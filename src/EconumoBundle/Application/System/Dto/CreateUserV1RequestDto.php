<?php

declare(strict_types=1);

namespace App\EconumoBundle\Application\System\Dto;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     required={"email", "name", "password"}
 * )
 */
class CreateUserV1RequestDto
{
    /**
     * @OA\Property(example="john@econumo.test")
     */
    public string $email;

    /**
     * @OA\Property(example="John")
     */
    public string $name;

    /**
     * @OA\Property(example="qwerty123")
     */
    public string $password;
}
