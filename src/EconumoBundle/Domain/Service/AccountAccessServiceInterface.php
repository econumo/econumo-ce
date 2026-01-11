<?php
declare(strict_types=1);

namespace App\EconumoBundle\Domain\Service;

use App\EconumoBundle\Domain\Entity\ValueObject\Id;

interface AccountAccessServiceInterface
{
    public function isAccessAllowed(Id $userId, Id $accountId): bool;

    public function canDeleteAccount(Id $userId, Id $accountId): bool;

    public function canUpdateAccount(Id $userId, Id $accountId): bool;

    public function canViewTransactions(Id $userId, Id $accountId): bool;

    public function checkViewTransactionsAccess(Id $userId, Id $accountId): void;

    public function canAddTransaction(Id $userId, Id $accountId): bool;

    public function canUpdateTransaction(Id $userId, Id $accountId): bool;

    public function canDeleteTransaction(Id $userId, Id $accountId): bool;

    public function canGenerateInvite(Id $userId, Id $accountId): bool;

    public function checkGenerateInviteAccess(Id $userId, Id $accountId): void;

    public function canAddPayee(Id $userId, Id $accountId): bool;

    public function checkAddPayee(Id $userId, Id $accountId): void;

    public function canAddCategory(Id $userId, Id $accountId): bool;

    public function checkAddCategory(Id $userId, Id $accountId): void;

    public function canAddTag(Id $userId, Id $accountId): bool;

    public function checkAddTag(Id $userId, Id $accountId): void;
}
