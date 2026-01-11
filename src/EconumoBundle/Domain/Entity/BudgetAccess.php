<?php

declare(strict_types=1);

namespace App\EconumoBundle\Domain\Entity;

use App\EconumoBundle\Domain\Entity\Budget;
use App\EconumoBundle\Domain\Entity\User;
use App\EconumoBundle\Domain\Entity\ValueObject\Id;
use App\EconumoBundle\Domain\Entity\ValueObject\UserRole;
use App\EconumoBundle\Domain\Traits\EntityTrait;
use DateTime;
use DateTimeImmutable;
use DateTimeInterface;

class BudgetAccess
{
    use EntityTrait;

    private Id $id;

    private bool $isAccepted = false;

    private DateTimeImmutable $createdAt;

    private DateTimeInterface $updatedAt;

    public function __construct(
        private Budget $budget,
        private User $user,
        private UserRole $role,
        DateTimeInterface $createdAt
    ) {
        $this->createdAt = DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $createdAt->format('Y-m-d H:i:s'));
        $this->updatedAt = DateTime::createFromFormat('Y-m-d H:i:s', $createdAt->format('Y-m-d H:i:s'));
    }

    public function getBudget(): Budget
    {
        return $this->budget;
    }

    public function getBudgetId(): Id
    {
        return $this->budget->getId();
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getUserId(): Id
    {
        return $this->user->getId();
    }

    public function getRole(): UserRole
    {
        return $this->role;
    }

    public function updateRole(UserRole $role): void
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

    public function isAccepted(): bool
    {
        return $this->isAccepted;
    }

    public function accept(): void
    {
        if (!$this->isAccepted) {
            $this->isAccepted = true;
            $this->updated();
        }
    }
}