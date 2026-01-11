<?php

declare(strict_types=1);

namespace App\EconumoBundle\Application\User\Dto;

use App\EconumoBundle\Application\User\Dto\CurrentUserResultDto;
use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     required={"user"}
 * )
 */
class RegisterUserV1ResultDto
{
    public CurrentUserResultDto $user;
}
