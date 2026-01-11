<?php

declare(strict_types=1);

namespace App\EconumoBundle\Domain\Factory;

use App\EconumoBundle\Domain\Entity\User;
use App\EconumoBundle\Domain\Entity\UserOption;

interface UserOptionFactoryInterface
{
    public function create(User $user, string $name, ?string $value): UserOption;
}
