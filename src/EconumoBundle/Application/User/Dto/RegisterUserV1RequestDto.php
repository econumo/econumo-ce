<?php

declare(strict_types=1);

namespace App\EconumoBundle\Application\User\Dto;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     required={"email", "password"}
 * )
 */
class RegisterUserV1RequestDto
{
    /**
     * @OA\Property(example="john@econumo.test")
     */
    public string $email;

    /**
     * @OA\Property(example="pass")
     */
    public string $password;

    /**
     * @OA\Property(example="John")
     */
    public string $name;
}
