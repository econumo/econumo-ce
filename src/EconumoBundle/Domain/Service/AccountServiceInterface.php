<?php
declare(strict_types=1);

namespace App\EconumoBundle\Domain\Service;

use App\EconumoBundle\Domain\Entity\Account;
use App\EconumoBundle\Domain\Entity\Transaction;
use App\EconumoBundle\Domain\Entity\ValueObject\AccountName;
use App\EconumoBundle\Domain\Entity\ValueObject\Icon;
use App\EconumoBundle\Domain\Entity\ValueObject\Id;
use App\EconumoBundle\Domain\Entity\ValueObject\DecimalNumber;
use App\EconumoBundle\Domain\Service\Dto\AccountDto;
use App\EconumoBundle\Domain\Service\Dto\AccountPositionDto;
use DateTimeInterface;

interface AccountServiceInterface
{
    public function create(AccountDto $dto): Account;

    public function delete(Id $id): void;

    public function update(Id $userId, Id $accountId, AccountName $name, Icon $icon = null, ?Id $currencyId = null): void;

    public function updateBalance(Id $accountId, DecimalNumber $balance, DateTimeInterface $updatedAt, ?string $comment = ''): ?Transaction;

    /**
     * @param Id $userId
     * @param AccountPositionDto[] $changes
     * @return void
     */
    public function orderAccounts(Id $userId, array $changes): void;

    public function getBalance(Id $accountId): DecimalNumber;

    /**
     * @param Id[] $accountsIds
     * @return DecimalNumber[]
     */
    public function getAccountsBalance(array $accountsIds): array;
}
