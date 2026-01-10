<?php

declare(strict_types=1);

namespace App\EconumoBundle\Domain\Service;

use App\EconumoBundle\Domain\Entity\Account;
use App\EconumoBundle\Domain\Entity\Transaction;
use App\EconumoBundle\Domain\Entity\ValueObject\AccountName;
use App\EconumoBundle\Domain\Entity\ValueObject\AccountType;
use App\EconumoBundle\Domain\Entity\ValueObject\Icon;
use App\EconumoBundle\Domain\Entity\ValueObject\Id;
use App\EconumoBundle\Domain\Entity\ValueObject\DecimalNumber;
use App\EconumoBundle\Domain\Exception\AccessDeniedException;
use App\EconumoBundle\Domain\Factory\AccountFactoryInterface;
use App\EconumoBundle\Domain\Factory\AccountOptionsFactoryInterface;
use App\EconumoBundle\Domain\Factory\TransactionFactoryInterface;
use App\EconumoBundle\Domain\Repository\AccountOptionsRepositoryInterface;
use App\EconumoBundle\Domain\Repository\AccountRepositoryInterface;
use App\EconumoBundle\Domain\Repository\CurrencyRepositoryInterface;
use App\EconumoBundle\Domain\Repository\FolderRepositoryInterface;
use App\EconumoBundle\Domain\Repository\TransactionRepositoryInterface;
use App\EconumoBundle\Domain\Service\Dto\AccountDto;
use DateTimeInterface;
use Throwable;

readonly class AccountService implements AccountServiceInterface
{
    public function __construct(
        private AccountRepositoryInterface $accountRepository,
        private AccountFactoryInterface $accountFactory,
        private TransactionServiceInterface $transactionService,
        private AccountOptionsFactoryInterface $accountOptionsFactory,
        private AccountOptionsRepositoryInterface $accountOptionsRepository,
        private CurrencyRepositoryInterface $currencyRepository,
        private AntiCorruptionServiceInterface $antiCorruptionService,
        private FolderRepositoryInterface $folderRepository,
        private TransactionFactoryInterface $transactionFactory,
        private TransactionRepositoryInterface $transactionRepository,
        private DatetimeServiceInterface $datetimeService,
    ) {
    }

    public function create(AccountDto $dto): Account
    {
        $this->antiCorruptionService->beginTransaction(__METHOD__);
        try {
            $userAccountOptions = $this->accountOptionsRepository->getByUserId($dto->userId);
            $position = 0;
            foreach ($userAccountOptions as $option) {
                if ($option->getPosition() > $position) {
                    $position = $option->getPosition();
                }
            }

            if ($position === 0) {
                $position = count($this->accountRepository->getAvailableForUserId($dto->userId));
            }

            $account = $this->accountFactory->create(
                $dto->userId,
                new AccountName($dto->name),
                new AccountType(AccountType::CREDIT_CARD),
                $dto->currencyId,
                new Icon($dto->icon)
            );
            $this->accountRepository->save([$account]);

            $accountOptions = $this->accountOptionsFactory->create($account->getId(), $dto->userId, $position);
            $this->accountOptionsRepository->save([$accountOptions]);

            $folder = $this->folderRepository->get($dto->folderId);
            if (!$folder->getUserId()->isEqual($dto->userId)) {
                throw new AccessDeniedException();
            }

            $folder->addAccount($account);
            $this->folderRepository->save([$folder]);

            if (!$dto->balance->isZero()) {
                $transaction = $this->transactionFactory->createTransaction(
                    $account->getId(),
                    $dto->balance,
                    $account->getCreatedAt()
                );
                $this->transactionRepository->save([$transaction]);
            }

            $this->antiCorruptionService->commit(__METHOD__);
        } catch (Throwable $throwable) {
            $this->antiCorruptionService->rollback(__METHOD__);
            throw $throwable;
        }

        return $account;
    }

    public function delete(Id $id): void
    {
        $account = $this->accountRepository->get($id);
        $account->delete();

        $this->accountRepository->save([$account]);
    }

    public function update(Id $userId, Id $accountId, AccountName $name, Icon $icon = null, ?Id $currencyId = null): void
    {
        $account = $this->accountRepository->get($accountId);
        $account->updateName($name);
        if ($icon instanceof Icon) {
            $account->updateIcon($icon);
        }
        if ($currencyId instanceof Id) {
            $account->updateCurrency($this->currencyRepository->getReference($currencyId));
        }

        $this->accountRepository->save([$account]);
    }

    public function updateBalance(
        Id $accountId,
        DecimalNumber $balance,
        DateTimeInterface $updatedAt,
        ?string $comment = ''
    ): ?Transaction {
        $actualBalance = $this->getBalance($accountId);
        if ($actualBalance->equals($balance)) {
            return null;
        }

        return $this->transactionService->updateBalance(
            $accountId,
            $actualBalance->sub($balance),
            $updatedAt,
            (string)$comment
        );
    }

    /**
     * @inheritDoc
     */
    public function orderAccounts(Id $userId, array $changes): void
    {
        $this->antiCorruptionService->beginTransaction(__METHOD__);
        try {
            $accounts = $this->accountRepository->getAvailableForUserId($userId);
            $accountOptions = $this->accountOptionsRepository->getByUserId($userId);
            $folders = $this->folderRepository->getByUserId($userId);

            $tmpOptions = [];
            foreach ($changes as $change) {
                $accountFound = null;
                foreach ($accounts as $account) {
                    if ($change->getId()->isEqual($account->getId())) {
                        $accountFound = $account;
                        break;
                    }
                }

                if (!$accountFound instanceof Account) {
                    continue;
                }

                foreach ($folders as $folder) {
                    if (!$change->getFolderId()->isEqual($folder->getId())) {
                        if ($folder->containsAccount($accountFound)) {
                            $folder->removeAccount($accountFound);
                        }
                    } elseif (!$folder->containsAccount($accountFound)) {
                        $folder->addAccount($accountFound);
                    }
                }

                $optionFound = false;
                foreach ($accountOptions as $accountOption) {
                    if ($change->getId()->isEqual($accountOption->getAccountId())) {
                        $accountOption->updatePosition($change->position);
                        $optionFound = true;
                        $tmpOptions[] = $accountOption;
                        break;
                    }
                }

                if (!$optionFound) {
                    $tmpOptions[] = $this->accountOptionsFactory->create(
                        $change->getId(),
                        $userId,
                        $change->position
                    );
                }
            }

            $this->accountOptionsRepository->save($tmpOptions);
            $this->folderRepository->save($folders);
            $this->antiCorruptionService->commit(__METHOD__);
        } catch (Throwable $throwable) {
            $this->antiCorruptionService->rollback(__METHOD__);
            throw $throwable;
        }
    }

    public function getChanged(Id $userId, DateTimeInterface $lastUpdate): array
    {
        $accounts = $this->accountRepository->getAvailableForUserId($userId);
        $result = [];
        foreach ($accounts as $account) {
            if ($account->getUpdatedAt() > $lastUpdate) {
                $result[] = $account;
            }
        }

        return $result;
    }

    public function getBalance(Id $accountId): DecimalNumber
    {
        $tomorrow = $this->datetimeService->getNextDay();
        return $this->transactionRepository->getAccountBalance(
            $accountId,
            $tomorrow
        );
    }

    public function getAccountsBalance(array $accountsIds): array
    {
        $tomorrow = $this->datetimeService->getNextDay();
        $balances = $this->accountRepository->getAccountsBalancesBeforeDate(
            $accountsIds,
            $tomorrow
        );
        $result = [];
        foreach ($balances as $balance) {
            $result[$balance['account_id']] = new DecimalNumber($balance['balance']);
        }

        return $result;
    }
}
