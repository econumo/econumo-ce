<?php

declare(strict_types=1);

namespace App\EconumoBundle\Application\User\Dto;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     required={"username", "password"}
 * )
 */
class LoginUserV1RequestDto
{
    /**
     * @var string
     * @OA\Property(example="username")
     */
    public string $username;

    /**
     * @var string
     * @OA\Property(example="password")
     */
    public string $password;
}
