<?php

declare(strict_types=1);

namespace App\EconumoBundle\Domain\Repository;

use App\EconumoBundle\Domain\Entity\AccountAccess;
use App\EconumoBundle\Domain\Entity\ValueObject\Id;
use App\EconumoBundle\Domain\Exception\NotFoundException;

interface AccountAccessRepositoryInterface
{
    /**
     * @return AccountAccess[]
     */
    public function getByAccount(Id $accountId): array;

    /**
     * @param AccountAccess[] $items
     */
    public function save(array $items): void;

    /**
     * @throws NotFoundException
     */
    public function get(Id $accountId, Id $userId): AccountAccess;

    public function delete(AccountAccess $accountAccess): void;

    /**
     * @return AccountAccess[]
     */
    public function getOwnedByUser(Id $userId): array;

    /**
     * @return AccountAccess[]
     */
    public function getReceivedAccess(Id $userId): array;

    /**
     * @return AccountAccess[]
     */
    public function getIssuedAccess(Id $userId): array;
}
