<?php

declare(strict_types=1);


namespace App\EconumoBundle\Application\Connection\Dto;

use App\EconumoBundle\Application\Connection\Dto\AccountAccessResultDto;
use App\EconumoBundle\Application\User\Dto\UserResultDto;
use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     required={"user", "accountAccess"}
 * )
 */
class ConnectionResultDto
{
    /**
     * @var UserResultDto
     * @OA\Property()
     */
    public UserResultDto $user;

    /**
     * @var AccountAccessResultDto[]
     * @OA\Property()
     */
    public array $sharedAccounts = [];
}
