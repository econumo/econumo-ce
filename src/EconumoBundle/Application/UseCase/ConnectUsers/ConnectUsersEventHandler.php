<?php

declare(strict_types=1);


namespace App\EconumoBundle\Application\UseCase\ConnectUsers;


use App\EconumoBundle\Domain\Events\UserRegisteredEvent;
use App\EconumoBundle\Domain\Repository\UserRepositoryInterface;
use App\EconumoBundle\Domain\Service\EconumoServiceInterface;
use App\EconumoBundle\Domain\Service\EventHandlerInterface;

readonly class ConnectUsersEventHandler implements EventHandlerInterface
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private EconumoServiceInterface $econumoService
    ) {
    }

    public function __invoke(UserRegisteredEvent $event): void
    {
        if (!$this->econumoService->connectUsers()) {
            return;
        }

        $users = $this->userRepository->getAll();
        $newUser = $this->userRepository->get($event->getUserId());
        $toSave = [
            $newUser->getId()->getValue() => $newUser
        ];
        foreach ($users as $user) {
            if ($user->getId()->isEqual($event->getUserId())) {
                continue;
            }

            $user->connectUser($newUser);
            $newUser->connectUser($user);
            $toSave[$user->getId()->getValue()] = $user;
        }

        $this->userRepository->save($toSave);
    }
}
