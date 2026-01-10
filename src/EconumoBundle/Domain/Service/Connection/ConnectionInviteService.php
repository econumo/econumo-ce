<?php

declare(strict_types=1);

namespace App\EconumoBundle\Domain\Service\Connection;

use Throwable;
use App\EconumoBundle\Domain\Entity\ConnectionInvite;
use App\EconumoBundle\Domain\Entity\ValueObject\ConnectionCode;
use App\EconumoBundle\Domain\Entity\ValueObject\Id;
use App\EconumoBundle\Domain\Exception\DomainException;
use App\EconumoBundle\Domain\Factory\ConnectionInviteFactoryInterface;
use App\EconumoBundle\Domain\Repository\ConnectionInviteRepositoryInterface;
use App\EconumoBundle\Domain\Repository\UserRepositoryInterface;
use App\EconumoBundle\Domain\Service\AntiCorruptionServiceInterface;
use App\EconumoBundle\Domain\Service\Connection\ConnectionInviteServiceInterface;

class ConnectionInviteService implements ConnectionInviteServiceInterface
{
    public function __construct(private readonly ConnectionInviteFactoryInterface $connectionInviteFactory, private readonly ConnectionInviteRepositoryInterface $connectionInviteRepository, private readonly UserRepositoryInterface $userRepository, private readonly AntiCorruptionServiceInterface $antiCorruptionService)
    {
    }

    public function generate(Id $userId): ConnectionInvite
    {
        $connectionInvite = $this->connectionInviteRepository->getByUser($userId);
        if (!$connectionInvite instanceof ConnectionInvite) {
            $connectionInvite = $this->connectionInviteFactory->create($this->userRepository->getReference($userId));
        }

        $connectionInvite->generateNewCode();
        $this->connectionInviteRepository->save([$connectionInvite]);
        return $connectionInvite;
    }

    public function delete(Id $userId): void
    {
        $connectionInvite = $this->connectionInviteRepository->getByUser($userId);
        if (!$connectionInvite instanceof ConnectionInvite) {
            return;
        }

        $connectionInvite->clearCode();
        $this->connectionInviteRepository->save([$connectionInvite]);
    }

    public function accept(Id $userId, ConnectionCode $code): void
    {
        $connectionInvite = $this->connectionInviteRepository->getByCode($code);
        if ($connectionInvite->getUserId()->isEqual($userId)) {
            throw new DomainException('Inviting yourself?');
        }

        $this->antiCorruptionService->beginTransaction(__METHOD__);
        try {
            $owner = $this->userRepository->get($connectionInvite->getUserId());
            $guest = $this->userRepository->get($userId);

            $owner->connectUser($guest);
            $guest->connectUser($owner);
            $this->userRepository->save([$owner, $guest]);

            $connectionInvite->clearCode();
            $this->connectionInviteRepository->save([$connectionInvite]);

            $this->antiCorruptionService->commit(__METHOD__);
        } catch (Throwable $throwable) {
            $this->antiCorruptionService->rollback(__METHOD__);
            throw $throwable;
        }
    }
}
