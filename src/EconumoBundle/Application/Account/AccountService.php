<?php

declare(strict_types=1);

namespace App\EconumoBundle\Application\Account;

use App\EconumoBundle\Domain\Entity\ValueObject\DecimalNumber;
use DateTimeImmutable;
use App\EconumoBundle\Application\Account\Assembler\CreateAccountV1ResultAssembler;
use App\EconumoBundle\Application\Account\Assembler\DeleteAccountV1ResultAssembler;
use App\EconumoBundle\Application\Account\Assembler\UpdateAccountV1ResultAssembler;
use App\EconumoBundle\Application\Account\Dto\CreateAccountV1RequestDto;
use App\EconumoBundle\Application\Account\Dto\CreateAccountV1ResultDto;
use App\EconumoBundle\Application\Account\Dto\DeleteAccountV1RequestDto;
use App\EconumoBundle\Application\Account\Dto\DeleteAccountV1ResultDto;
use App\EconumoBundle\Application\Account\Dto\UpdateAccountV1RequestDto;
use App\EconumoBundle\Application\Account\Dto\UpdateAccountV1ResultDto;
use App\EconumoBundle\Application\Exception\AccessDeniedException;
use App\EconumoBundle\Domain\Entity\ValueObject\AccountName;
use App\EconumoBundle\Domain\Entity\ValueObject\Icon;
use App\EconumoBundle\Domain\Entity\ValueObject\Id;
use App\EconumoBundle\Domain\Repository\AccountRepositoryInterface;
use App\EconumoBundle\Domain\Service\AccountAccessServiceInterface;
use App\EconumoBundle\Domain\Service\AccountServiceInterface;
use App\EconumoBundle\Domain\Service\Connection\ConnectionAccountServiceInterface;
use App\EconumoBundle\Domain\Service\Dto\AccountDto;
use Symfony\Contracts\Translation\TranslatorInterface;

class AccountService
{
    public function __construct(
        private readonly CreateAccountV1ResultAssembler $createAccountV1ResultAssembler,
        private readonly AccountServiceInterface $accountService,
        private readonly DeleteAccountV1ResultAssembler $deleteAccountV1ResultAssembler,
        private readonly UpdateAccountV1ResultAssembler $updateAccountV1ResultAssembler,
        private readonly AccountRepositoryInterface $accountRepository,
        private readonly AccountAccessServiceInterface $accountAccessService,
        private readonly TranslatorInterface $translator,
        private readonly ConnectionAccountServiceInterface $connectionAccountService
    ) {
    }

    public function createAccount(
        CreateAccountV1RequestDto $dto,
        Id $userId
    ): CreateAccountV1ResultDto {
        $accountDto = new AccountDto();
        $accountDto->userId = $userId;
        $accountDto->name = $dto->name;
        $accountDto->currencyId = new Id($dto->currencyId);
        $accountDto->balance = new DecimalNumber($dto->balance);
        $accountDto->icon = $dto->icon;
        $accountDto->folderId = new Id($dto->folderId);

        $account = $this->accountService->create($accountDto);
        return $this->createAccountV1ResultAssembler->assemble($dto, $userId, $account, $accountDto->balance);
    }

    public function deleteAccount(
        DeleteAccountV1RequestDto $dto,
        Id $userId
    ): DeleteAccountV1ResultDto {
        $accountId = new Id($dto->id);
        if (!$this->accountAccessService->canDeleteAccount($userId, $accountId)) {
            throw new AccessDeniedException();
        }

        $account = $this->accountRepository->get($accountId);
        if ($account->getUserId()->isEqual($userId)) {
            $this->accountService->delete($accountId);
        } else {
            $this->connectionAccountService->revokeAccountAccess($userId, $accountId);
        }

        return $this->deleteAccountV1ResultAssembler->assemble($dto);
    }

    public function updateAccount(
        UpdateAccountV1RequestDto $dto,
        Id $userId
    ): UpdateAccountV1ResultDto {
        $accountId = new Id($dto->id);
        if (!$this->accountAccessService->canUpdateAccount($userId, $accountId)) {
            throw new AccessDeniedException();
        }

        $currencyId = $dto->currencyId !== null ? new Id($dto->currencyId) : null;
        $this->accountService->update(
            $userId,
            $accountId,
            new AccountName($dto->name),
            new Icon($dto->icon),
            $currencyId
        );
        $updatedAt = DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $dto->updatedAt);
        $transaction = $this->accountService->updateBalance(
            $accountId,
            new DecimalNumber($dto->balance),
            $updatedAt,
            $this->translator->trans('account.correction.message')
        );
        $account = $this->accountRepository->get($accountId);
        return $this->updateAccountV1ResultAssembler->assemble($dto, $userId, $account, $transaction);
    }
}
