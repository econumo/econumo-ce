<?php

declare(strict_types=1);

namespace App\EconumoBundle\Domain\Factory;

use App\EconumoBundle\Domain\Entity\UserPasswordRequest;
use App\EconumoBundle\Domain\Entity\ValueObject\Id;

interface PasswordUserRequestFactoryInterface
{
    public function create(Id $userId): UserPasswordRequest;
}
