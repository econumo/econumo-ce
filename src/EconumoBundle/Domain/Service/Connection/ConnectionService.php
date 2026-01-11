<?php

declare(strict_types=1);


namespace App\EconumoBundle\Domain\Service\Connection;

use App\EconumoBundle\Domain\Entity\ValueObject\Id;
use App\EconumoBundle\Domain\Exception\DomainException;
use App\EconumoBundle\Domain\Repository\UserRepositoryInterface;
use App\EconumoBundle\Domain\Service\AntiCorruptionServiceInterface;
use App\EconumoBundle\Domain\Service\Budget\BudgetServiceInterface;
use App\EconumoBundle\Domain\Service\Budget\BudgetSharedAccessServiceInterface;
use Throwable;

readonly class ConnectionService implements ConnectionServiceInterface
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private AntiCorruptionServiceInterface $antiCorruptionService,
        private ConnectionAccountServiceInterface $connectionAccountService,
        private BudgetServiceInterface $budgetService,
        private BudgetSharedAccessServiceInterface $budgetAccess
    ) {
    }

    public function getUserList(Id $userId): iterable
    {
        $user = $this->userRepository->get($userId);
        return $user->getConnections();
    }

    public function delete(Id $initiatorUserId, Id $connectedUserId): void
    {
        $initiator = $this->userRepository->get($initiatorUserId);
        $connectedUser = $this->userRepository->get($connectedUserId);
        if ($initiator->getId()->isEqual($connectedUser->getId())) {
            throw new DomainException('Deleting yourself?');
        }

        $this->antiCorruptionService->beginTransaction(__METHOD__);
        try {
            foreach ($this->connectionAccountService->getReceivedAccountAccess($initiator->getId()) as $accountAccess) {
                if ($accountAccess->getAccount()->getUserId()->isEqual($connectedUser->getId())) {
                    $this->connectionAccountService->revokeAccountAccess($accountAccess->getUserId(), $accountAccess->getAccountId());
                }
            }

            foreach ($this->connectionAccountService->getIssuedAccountAccess($initiator->getId()) as $accountAccess) {
                if ($accountAccess->getUserId()->isEqual($connectedUser->getId())) {
                    $this->connectionAccountService->revokeAccountAccess($accountAccess->getUserId(), $accountAccess->getAccountId());
                }
            }

            foreach($this->budgetService->getBudgetList($initiatorUserId) as $budget) {
                foreach ($budget->access as $budgetUserAccess) {
                    if ($budgetUserAccess->id->isEqual($connectedUserId)) {
                        $this->budgetAccess->revokeAccess($budget->id, $connectedUserId);
                    }
                }
            }

            foreach($this->budgetService->getBudgetList($connectedUserId) as $budget) {
                foreach ($budget->access as $budgetUserAccess) {
                    if ($budgetUserAccess->id->isEqual($initiatorUserId)) {
                        $this->budgetAccess->revokeAccess($budget->id, $initiatorUserId);
                    }
                }
            }

            $initiator->deleteConnection($connectedUser);
            $connectedUser->deleteConnection($initiator);
            $this->userRepository->save([$initiator, $connectedUser]);

            $this->antiCorruptionService->commit(__METHOD__);
        } catch (Throwable $throwable) {
            $this->antiCorruptionService->rollback(__METHOD__);
            throw $throwable;
        }
    }
}
