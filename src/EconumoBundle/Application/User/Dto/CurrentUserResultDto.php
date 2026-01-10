<?php

declare(strict_types=1);

namespace App\EconumoBundle\Application\User\Dto;

use App\EconumoBundle\Application\User\Dto\OptionResultDto;
use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     required={"id", "name", "email", "avatar", "options"}
 * )
 */
class CurrentUserResultDto
{
    /**
     * User id
     * @OA\Property(example="f680553f-6b40-407d-a528-5123913be0aa")
     */
    public string $id;

    /**
     * User name
     * @var string
     * @OA\Property(example="John")
     */
    public string $name;

    /**
     * User e-mail
     * @var string
     * @OA\Property(example="john@econumo.test")
     */
    public string $email;

    /**
     * User avatar
     * @var string
     * @OA\Property(example="https://www.gravatar.com/avatar/f888aa10236977f30255dea605ec99d0")
     */
    public string $avatar;

    /**
     * @var OptionResultDto[]
     * @OA\Property()
     */
    public array $options = [];

    /**
     * @deprecated
     * Currency
     * @var string
     * @OA\Property(example="USD")
     */
    public string $currency;

    /**
     * @deprecated
     * Report period
     * @var string
     * @OA\Property(example="monthly")
     */
    public string $reportPeriod;
}
