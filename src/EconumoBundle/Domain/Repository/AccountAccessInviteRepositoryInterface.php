<?php

declare(strict_types=1);

namespace App\EconumoBundle\Domain\Repository;

use App\EconumoBundle\Domain\Entity\AccountAccessInvite;
use App\EconumoBundle\Domain\Entity\ValueObject\Id;
use App\EconumoBundle\Domain\Exception\NotFoundException;

interface AccountAccessInviteRepositoryInterface
{
    /**
     * @param AccountAccessInvite[] $items
     */
    public function save(array $items): void;

    public function get(Id $accountId, Id $recipientId): AccountAccessInvite;

    public function getByUserAndCode(Id $userId, string $code): AccountAccessInvite;

    public function delete(AccountAccessInvite $invite): void;

    /**
     * @return AccountAccessInvite[]
     */
    public function getUnacceptedByUser(Id $userId): array;
}
