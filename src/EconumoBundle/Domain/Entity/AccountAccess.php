<?php

declare(strict_types=1);

namespace App\EconumoBundle\Domain\Entity;

use App\EconumoBundle\Domain\Entity\User;
use App\EconumoBundle\Domain\Entity\ValueObject\AccountUserRole;
use App\EconumoBundle\Domain\Entity\Account;
use App\EconumoBundle\Domain\Entity\ValueObject\Id;
use App\EconumoBundle\Domain\Traits\EntityTrait;
use DateTime;
use DateTimeImmutable;
use DateTimeInterface;

class AccountAccess
{
    use EntityTrait;

    private Id $id;

    private DateTimeImmutable $createdAt;

    private DateTimeInterface $updatedAt;

    public function __construct(
        private Account $account,
        private User $user,
        private AccountUserRole $role,
        DateTimeInterface $createdAt
    ) {
        $this->createdAt = DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $createdAt->format('Y-m-d H:i:s'));
        $this->updatedAt = DateTime::createFromFormat('Y-m-d H:i:s', $createdAt->format('Y-m-d H:i:s'));
    }

    public function getAccount(): Account
    {
        return $this->account;
    }

    public function getAccountId(): Id
    {
        return $this->account->getId();
    }

    public function getUserId(): Id
    {
        return $this->user->getId();
    }

    public function getRole(): AccountUserRole
    {
        return $this->role;
    }

    public function updateRole(AccountUserRole $role): void
    {
        if (!$this->role->isEqual($role)) {
            $this->role = $role;
            $this->updated();
        }
    }

    private function updated(): void
    {
        $this->updatedAt = new DateTime();
    }
}
