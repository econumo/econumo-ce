<?php

declare(strict_types=1);

namespace App\EconumoBundle\Domain\Service;

use App\EconumoBundle\Domain\Entity\ValueObject\Email;
use App\EconumoBundle\Domain\Entity\ValueObject\Identifier;
use App\EconumoBundle\Domain\Entity\ValueObject\UserPasswordRequestCode;
use App\EconumoBundle\Domain\Exception\UserPasswordRequestExpiredException;
use App\EconumoBundle\Domain\Factory\UserPasswordRequestFactory;
use App\EconumoBundle\Domain\Repository\UserPasswordRequestRepositoryInterface;
use App\EconumoBundle\Domain\Repository\UserRepositoryInterface;
use App\EconumoBundle\Domain\Service\AntiCorruptionServiceInterface;
use App\EconumoBundle\Domain\Service\PasswordUserReminderServiceInterface;
use App\EconumoBundle\Domain\Service\User\UserPasswordServiceInterface;
use App\EconumoBundle\Domain\Service\EmailServiceInterface;
use App\EconumoBundle\Domain\Service\EventDispatcherInterface;
use Throwable;

readonly class PasswordUserReminderService implements PasswordUserReminderServiceInterface
{
    public function __construct(
        private UserPasswordRequestFactory $userPasswordRequestFactory,
        private UserPasswordRequestRepositoryInterface $userPasswordRequestRepository,
        private UserRepositoryInterface $userRepository,
        private EventDispatcherInterface $eventDispatcher,
        private AntiCorruptionServiceInterface $antiCorruptionService,
        private UserPasswordServiceInterface $userPasswordService,
        private EmailServiceInterface $emailService,
        private EncodeServiceInterface $encodeService,
    ) {
    }

    public function remindPassword(Email $email): void
    {
        $user = $this->userRepository->loadByIdentifier(new Identifier($this->encodeService->hash($email->getValue())));
        $this->antiCorruptionService->beginTransaction(__METHOD__);
        try {
            $this->userPasswordRequestRepository->removeUserCodes($user->getId());
            $userPasswordRequest = $this->userPasswordRequestFactory->create($user->getId());
            $this->userPasswordRequestRepository->save([$userPasswordRequest]);
            $this->emailService->sendResetPasswordConfirmationCode($email, $user->getId());
            $this->eventDispatcher->dispatchAll($userPasswordRequest->releaseEvents());

            $this->antiCorruptionService->commit(__METHOD__);
        } catch (Throwable $throwable) {
            $this->antiCorruptionService->rollback(__METHOD__);
            throw $throwable;
        }
    }

    public function resetPassword(Email $email, UserPasswordRequestCode $code, string $password): void
    {
        $user = $this->userRepository->loadByIdentifier(new Identifier($this->encodeService->hash($email->getValue())));
        $this->antiCorruptionService->beginTransaction(__METHOD__);
        try {
            $userPasswordRequest = $this->userPasswordRequestRepository->getByUserAndCode($user->getId(), $code);
            if ($userPasswordRequest->isExpired()) {
                throw new UserPasswordRequestExpiredException();
            }

            $this->userPasswordService->updatePassword($user->getId(), $password);
            $this->userPasswordRequestRepository->delete($userPasswordRequest);
            $this->antiCorruptionService->commit(__METHOD__);
        } catch (Throwable $throwable) {
            $this->antiCorruptionService->rollback(__METHOD__);
            throw $throwable;
        }
    }
}
