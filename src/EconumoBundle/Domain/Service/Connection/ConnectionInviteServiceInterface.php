<?php

declare(strict_types=1);


namespace App\EconumoBundle\Domain\Service\Connection;

use App\EconumoBundle\Domain\Entity\ConnectionInvite;
use App\EconumoBundle\Domain\Entity\ValueObject\ConnectionCode;
use App\EconumoBundle\Domain\Entity\ValueObject\Id;

interface ConnectionInviteServiceInterface
{
    public function generate(Id $userId): ConnectionInvite;

    public function delete(Id $userId): void;

    public function accept(Id $userId, ConnectionCode $code): void;
}
