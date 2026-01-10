<?php

declare(strict_types=1);

namespace App\EconumoBundle\Domain\Factory;

use App\EconumoBundle\Domain\Entity\ConnectionInvite;
use App\EconumoBundle\Domain\Entity\User;
use App\EconumoBundle\Domain\Factory\ConnectionInviteFactoryInterface;

class ConnectionInviteFactory implements ConnectionInviteFactoryInterface
{
    public function create(User $user): ConnectionInvite
    {
        return new ConnectionInvite($user);
    }
}
