<?php

declare(strict_types=1);

namespace App\EconumoBundle\Application\User\Dto;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     required={"oldPassword", "newPassword"}
 * )
 */
class UpdatePasswordV1RequestDto
{
    /**
     * @OA\Property(example="pass")
     */
    public string $oldPassword;

    /**
     * @OA\Property(example="new_pass")
     */
    public string $newPassword;
}
