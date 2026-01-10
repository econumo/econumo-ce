<?php

declare(strict_types=1);

namespace App\EconumoBundle\Domain\Service;

use App\EconumoBundle\Domain\Entity\ValueObject\Id;
use App\EconumoBundle\Domain\Exception\AccessDeniedException;
use App\EconumoBundle\Domain\Exception\NotFoundException;
use App\EconumoBundle\Domain\Repository\AccountAccessRepositoryInterface;
use App\EconumoBundle\Domain\Repository\AccountRepositoryInterface;
use App\EconumoBundle\Domain\Service\AccountAccessServiceInterface;

class AccountAccessService implements AccountAccessServiceInterface
{
    public function __construct(private readonly AccountAccessRepositoryInterface $accountAccessRepository, private readonly AccountRepositoryInterface $accountRepository)
    {
    }

    public function isAccessAllowed(Id $userId, Id $accountId): bool
    {
        return $this->hasAccess($userId, $accountId);
    }

    public function canDeleteAccount(Id $userId, Id $accountId): bool
    {
        return $this->isAccessAllowed($userId, $accountId);
    }

    public function canUpdateAccount(Id $userId, Id $accountId): bool
    {
        return $this->isAdmin($userId, $accountId);
    }

    public function canViewTransactions(Id $userId, Id $accountId): bool
    {
        return $this->hasAccess($userId, $accountId);
    }

    public function canAddTransaction(Id $userId, Id $accountId): bool
    {
        return $this->isUser($userId, $accountId);
    }

    public function canUpdateTransaction(Id $userId, Id $accountId): bool
    {
        return $this->isUser($userId, $accountId);
    }

    public function canDeleteTransaction(Id $userId, Id $accountId): bool
    {
        return $this->isUser($userId, $accountId);
    }

    public function canGenerateInvite(Id $userId, Id $accountId): bool
    {
        return $this->isOwner($userId, $accountId);
    }

    private function isOwner(Id $userId, Id $accountId): bool
    {
        $account = $this->accountRepository->get($accountId);
        return $account->getUserId()->isEqual($userId);
    }

    private function isAdmin(Id $userId, Id $accountId): bool
    {
        $account = $this->accountRepository->get($accountId);
        if ($account->getUserId()->isEqual($userId)) {
            return true;
        }

        try {
            $access = $this->accountAccessRepository->get($accountId, $userId);
        } catch (NotFoundException) {
            return false;
        }

        return $access->getRole()->isAdmin();
    }

    private function isUser(Id $userId, Id $accountId): bool
    {
        $account = $this->accountRepository->get($accountId);
        if ($account->getUserId()->isEqual($userId)) {
            return true;
        }

        try {
            $access = $this->accountAccessRepository->get($accountId, $userId);
        } catch (NotFoundException) {
            return false;
        }

        return $access->getRole()->isAdmin() || $access->getRole()->isUser();
    }

    private function hasAccess(Id $userId, Id $accountId): bool
    {
        $account = $this->accountRepository->get($accountId);
        if ($account->getUserId()->isEqual($userId)) {
            return true;
        }

        try {
            $this->accountAccessRepository->get($accountId, $userId);
        } catch (NotFoundException) {
            return false;
        }

        return true;
    }

    public function checkGenerateInviteAccess(Id $userId, Id $accountId): void
    {
        if (!$this->canGenerateInvite($userId, $accountId)) {
            throw new AccessDeniedException('Access is not allowed');
        }
    }

    public function checkViewTransactionsAccess(Id $userId, Id $accountId): void
    {
        if (!$this->canViewTransactions($userId, $accountId)) {
            throw new AccessDeniedException('Access is not allowed');
        }
    }

    public function canAddPayee(Id $userId, Id $accountId): bool
    {
        return $this->isAdmin($userId, $accountId);
    }

    public function checkAddPayee(Id $userId, Id $accountId): void
    {
        if (!$this->canAddPayee($userId, $accountId)) {
            throw new AccessDeniedException('Access is not allowed');
        }
    }

    public function canAddCategory(Id $userId, Id $accountId): bool
    {
        return $this->isAdmin($userId, $accountId);
    }

    public function checkAddCategory(Id $userId, Id $accountId): void
    {
        if (!$this->canAddCategory($userId, $accountId)) {
            throw new AccessDeniedException('Access is not allowed');
        }
    }

    public function canAddTag(Id $userId, Id $accountId): bool
    {
        return $this->isAdmin($userId, $accountId);
    }

    public function checkAddTag(Id $userId, Id $accountId): void
    {
        if (!$this->canAddTag($userId, $accountId)) {
            throw new AccessDeniedException('Access is not allowed');
        }
    }
}
