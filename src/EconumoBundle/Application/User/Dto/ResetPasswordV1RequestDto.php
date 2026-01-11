<?php

declare(strict_types=1);

namespace App\EconumoBundle\Application\User\Dto;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     required={"username", "code", "password"}
 * )
 */
class ResetPasswordV1RequestDto
{
    /**
     * @OA\Property(example="john@econumo.test")
     */
    public string $username;

    /**
     * @OA\Property(example="12345")
     */
    public string $code;

    /**
     * @OA\Property(example="password")
     */
    public string $password;
}
