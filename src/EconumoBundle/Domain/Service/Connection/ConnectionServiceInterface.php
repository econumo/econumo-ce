<?php

declare(strict_types=1);

namespace App\EconumoBundle\Domain\Service\Connection;

use App\EconumoBundle\Domain\Entity\User;
use App\EconumoBundle\Domain\Entity\ValueObject\Id;

interface ConnectionServiceInterface
{
    /**
     * @param Id $userId
     * @return User[]
     */
    public function getUserList(Id $userId): iterable;

    public function delete(Id $initiatorUserId, Id $connectedUserId): void;
}
