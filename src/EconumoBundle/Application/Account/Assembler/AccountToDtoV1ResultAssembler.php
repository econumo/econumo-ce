<?php

declare(strict_types=1);

namespace App\EconumoBundle\Application\Account\Assembler;

use App\EconumoBundle\Application\Account\Dto\AccountResultDto;
use App\EconumoBundle\Application\Currency\Assembler\CurrencyToDtoV1ResultAssembler;
use App\EconumoBundle\Application\User\Assembler\UserIdToDtoResultAssembler;
use App\EconumoBundle\Domain\Entity\Account;
use App\EconumoBundle\Domain\Entity\ValueObject\Id;
use App\EconumoBundle\Domain\Entity\ValueObject\DecimalNumber;
use App\EconumoBundle\Domain\Repository\AccountOptionsRepositoryInterface;
use App\EconumoBundle\Domain\Repository\FolderRepositoryInterface;

readonly class AccountToDtoV1ResultAssembler
{
    public function __construct(
        private AccountIdToSharedAccessResultAssembler $accountIdToSharedAccessResultAssembler,
        private CurrencyToDtoV1ResultAssembler $currencyToDtoV1ResultAssembler,
        private FolderRepositoryInterface $folderRepository,
        private UserIdToDtoResultAssembler $userIdToDtoResultAssembler,
        private AccountOptionsRepositoryInterface $accountOptionsRepository
    ) {
    }

    public function assemble(Id $userId, Account $account, DecimalNumber $balance): AccountResultDto
    {
        $item = new AccountResultDto();
        $item->id = $account->getId()->getValue();
        $item->owner = $this->userIdToDtoResultAssembler->assemble($account->getUserId());
        $item->folderId = null;
        $folders = $this->folderRepository->getByUserId($userId);
        foreach ($folders as $folder) {
            if ($folder->containsAccount($account)) {
                $item->folderId = $folder->getId()->getValue();
                break;
            }
        }

        $item->name = $account->getName()->getValue();
        $item->currency = $this->currencyToDtoV1ResultAssembler->assemble($account->getCurrency());
        $item->balance = $balance->getValue();
        $item->type = $account->getType()->getValue();
        $item->icon = $account->getIcon()->getValue();
        $item->sharedAccess = $this->accountIdToSharedAccessResultAssembler->assemble($account->getId());
        $options = $this->accountOptionsRepository->get($account->getId(), $userId);
        $item->position = $options->getPosition();

        return $item;
    }
}
