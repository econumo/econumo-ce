<?php

declare(strict_types=1);


namespace App\EconumoBundle\Domain\Repository;

use App\EconumoBundle\Domain\Entity\ConnectionInvite;
use App\EconumoBundle\Domain\Entity\ValueObject\ConnectionCode;
use App\EconumoBundle\Domain\Entity\ValueObject\Id;
use App\EconumoBundle\Domain\Exception\NotFoundException;

interface ConnectionInviteRepositoryInterface
{
    /**
     * @param ConnectionInvite[] $items
     */
    public function save(array $items): void;

    public function delete(ConnectionInvite $item): void;

    public function getByUser(Id $userId): ?ConnectionInvite;

    /**
     * @throws NotFoundException
     */
    public function getByCode(ConnectionCode $code): ConnectionInvite;
}
