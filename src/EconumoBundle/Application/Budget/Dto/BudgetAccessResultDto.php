<?php
declare(strict_types=1);

namespace App\EconumoBundle\Application\Budget\Dto;

use App\EconumoBundle\Application\User\Dto\UserResultDto;
use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     required={"user", "role", "isAccepted"}
 * )
 */
class BudgetAccessResultDto
{
    /**
     * User
     * @var UserResultDto
     * @OA\Property()
     */
    public UserResultDto $user;

    /**
     * User role
     * @var string
     * @OA\Property(example="admin")
     */
    public string $role;

    /**
     * Is invite accepted?
     * @var int
     * @OA\Property(example="0")
     */
    public int $isAccepted;
}