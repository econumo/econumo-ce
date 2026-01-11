<?php

declare(strict_types=1);

namespace App\EconumoBundle\Domain\Service;

use App\EconumoBundle\Domain\Entity\Currency;
use App\EconumoBundle\Domain\Repository\BudgetRepositoryInterface;
use App\EconumoBundle\Domain\Repository\CurrencyRepositoryInterface;
use Throwable;
use App\EconumoBundle\Domain\Entity\User;
use App\EconumoBundle\Domain\Entity\UserOption;
use App\EconumoBundle\Domain\Entity\ValueObject\CurrencyCode;
use App\EconumoBundle\Domain\Entity\ValueObject\Email;
use App\EconumoBundle\Domain\Entity\ValueObject\FolderName;
use App\EconumoBundle\Domain\Entity\ValueObject\Id;
use App\EconumoBundle\Domain\Entity\ValueObject\ReportPeriod;
use App\EconumoBundle\Domain\Exception\NotFoundException;
use App\EconumoBundle\Domain\Exception\UserRegisteredException;
use App\EconumoBundle\Domain\Factory\ConnectionInviteFactoryInterface;
use App\EconumoBundle\Domain\Factory\FolderFactoryInterface;
use App\EconumoBundle\Domain\Factory\UserFactoryInterface;
use App\EconumoBundle\Domain\Factory\UserOptionFactoryInterface;
use App\EconumoBundle\Domain\Repository\ConnectionInviteRepositoryInterface;
use App\EconumoBundle\Domain\Repository\FolderRepositoryInterface;
use App\EconumoBundle\Domain\Repository\UserOptionRepositoryInterface;
use App\EconumoBundle\Domain\Repository\UserRepositoryInterface;
use App\EconumoBundle\Domain\Service\Translation\TranslationServiceInterface;

readonly class UserService implements UserServiceInterface
{
    public function __construct(
        private UserFactoryInterface $userFactory,
        private UserRepositoryInterface $userRepository,
        private EventDispatcherInterface $eventDispatcher,
        private FolderFactoryInterface $folderFactory,
        private FolderRepositoryInterface $folderRepository,
        private AntiCorruptionServiceInterface $antiCorruptionService,
        private TranslationServiceInterface $translator,
        private ConnectionInviteFactoryInterface $connectionInviteFactory,
        private ConnectionInviteRepositoryInterface $connectionInviteRepository,
        private UserOptionFactoryInterface $userOptionFactory,
        private UserOptionRepositoryInterface $userOptionRepository,
        private EncodeServiceInterface $encodeService,
        private CurrencyRepositoryInterface $currencyRepository,
        private BudgetRepositoryInterface $budgetRepository
    )
    {
    }

    public function register(Email $email, string $password, string $name): User
    {
        try {
            $this->userRepository->getByEmail($email);
            throw new UserRegisteredException();
        } catch (NotFoundException) {
        }

        $this->antiCorruptionService->beginTransaction(__METHOD__);
        try {
            $user = $this->userFactory->create($name, $email, $password);
            $this->userRepository->save([$user]);

            $folder = $this->folderFactory->create($user->getId(), new FolderName($this->translator->trans('account.folder.all_accounts')));
            $this->folderRepository->save([$folder]);

            $connectionInvite = $this->connectionInviteFactory->create($user);
            $this->connectionInviteRepository->save([$connectionInvite]);

            $this->userOptionRepository->save(
                [
                    $this->userOptionFactory->create($user, UserOption::CURRENCY, UserOption::DEFAULT_CURRENCY),
                    $this->userOptionFactory->create($user, UserOption::REPORT_PERIOD, UserOption::DEFAULT_REPORT_PERIOD),
                    $this->userOptionFactory->create($user, UserOption::BUDGET, null),
                    $this->userOptionFactory->create($user, UserOption::ONBOARDING, UserOption::ONBOARDING_VALUE_STARTED)
                ]
            );

            $this->antiCorruptionService->commit(__METHOD__);
        } catch (Throwable $throwable) {
            $this->antiCorruptionService->rollback(__METHOD__);
            throw $throwable;
        }

        $this->eventDispatcher->dispatchAll($user->releaseEvents());
        // do not send first folder creation event
//        $this->eventDispatcher->dispatchAll($folder->releaseEvents());

        return $user;
    }

    public function updateName(Id $userId, string $name): void
    {
        $user = $this->userRepository->get($userId);
        $user->updateName($name);

        $this->userRepository->save([$user]);
    }

    public function updateCurrency(Id $userId, CurrencyCode $currencyCode): void
    {
        $currency = $this->currencyRepository->getByCode($currencyCode);
        if (!$currency instanceof Currency) {
            throw new NotFoundException(sprintf('Currency %s not found', $currencyCode->getValue()));
        }

        $this->antiCorruptionService->beginTransaction(__METHOD__);
        try {
            $user = $this->userRepository->get($userId);
            $user->updateCurrency($currencyCode);
            $this->userRepository->save([$user]);
            $this->antiCorruptionService->commit(__METHOD__);
        } catch (Throwable $throwable) {
            $this->antiCorruptionService->rollback(__METHOD__);
            throw $throwable;
        }
    }

    public function updateReportPeriod(Id $userId, ReportPeriod $reportPeriod): void
    {
        $this->antiCorruptionService->beginTransaction(__METHOD__);
        try {
            $user = $this->userRepository->get($userId);
            $user->updateReportPeriod($reportPeriod);
            $this->userRepository->save([$user]);
            $this->antiCorruptionService->commit(__METHOD__);
        } catch (Throwable $throwable) {
            $this->antiCorruptionService->rollback(__METHOD__);
            throw $throwable;
        }
    }

    public function updateBudget(Id $userId, ?Id $budgetId): void
    {
        $budget = $this->budgetRepository->get($budgetId);
        $this->antiCorruptionService->beginTransaction(__METHOD__);
        try {
            $user = $this->userRepository->get($userId);
            $user->updateDefaultBudget($budget->getId());
            $this->userRepository->save([$user]);
            $this->antiCorruptionService->commit(__METHOD__);
        } catch (Throwable $throwable) {
            $this->antiCorruptionService->rollback(__METHOD__);
            throw $throwable;
        }
    }

    public function completeOnboarding(Id $userId): void
    {
        $this->antiCorruptionService->beginTransaction(__METHOD__);
        try {
            $user = $this->userRepository->get($userId);
            $user->completeOnboarding();
            $this->userRepository->save([$user]);
            $this->antiCorruptionService->commit(__METHOD__);
        } catch (Throwable $throwable) {
            $this->antiCorruptionService->rollback(__METHOD__);
            throw $throwable;
        }
    }

    public function updateEmail(Id $userId, Email $email): void
    {
        $user = $this->userRepository->get($userId);

        $emailValue = strtolower($email->getValue());
        $identifier = $this->encodeService->hash($emailValue);
        $encodedEmail = $this->encodeService->encode($email->getValue());
        $avatarUrl = sprintf('https://www.gravatar.com/avatar/%s', md5($emailValue));

        $user->updateUserIdentifier($identifier);
        $user->updateEmail($encodedEmail);
        $user->updateAvatarUrl($avatarUrl);

        $this->userRepository->save([$user]);
    }
}
