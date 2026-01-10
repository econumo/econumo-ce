<?php

declare(strict_types=1);


namespace App\EconumoBundle\Domain\Factory;

use App\EconumoBundle\Domain\Entity\ConnectionInvite;
use App\EconumoBundle\Domain\Entity\User;

interface ConnectionInviteFactoryInterface
{
    public function create(User $user): ConnectionInvite;
}
