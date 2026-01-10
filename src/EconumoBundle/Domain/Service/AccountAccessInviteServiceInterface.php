<?php

declare(strict_types=1);

namespace App\EconumoBundle\Domain\Service;

use App\EconumoBundle\Domain\Entity\Account;
use App\EconumoBundle\Domain\Entity\AccountAccessInvite;
use App\EconumoBundle\Domain\Entity\ValueObject\AccountUserRole;
use App\EconumoBundle\Domain\Entity\ValueObject\Email;
use App\EconumoBundle\Domain\Entity\ValueObject\Id;

interface AccountAccessInviteServiceInterface
{
    public function generate(Id $userId, Id $accountId, Email $recipientUsername, AccountUserRole $role): AccountAccessInvite;

    public function accept(Id $userId, string $code): Account;
}
