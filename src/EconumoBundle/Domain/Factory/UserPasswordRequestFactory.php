<?php

declare(strict_types=1);

namespace App\EconumoBundle\Domain\Factory;

use App\EconumoBundle\Domain\Entity\UserPasswordRequest;
use App\EconumoBundle\Domain\Entity\ValueObject\Id;
use App\EconumoBundle\Domain\Entity\ValueObject\UserPasswordRequestCode;
use App\EconumoBundle\Domain\Factory\PasswordUserRequestFactoryInterface;
use App\EconumoBundle\Domain\Repository\UserPasswordRequestRepositoryInterface;
use App\EconumoBundle\Domain\Repository\UserRepositoryInterface;
use App\EconumoBundle\Domain\Service\DatetimeServiceInterface;

readonly class UserPasswordRequestFactory implements PasswordUserRequestFactoryInterface
{
    public function __construct(private DatetimeServiceInterface $datetimeService, private UserPasswordRequestRepositoryInterface $passwordUserRequestRepository, private UserRepositoryInterface $userRepository)
    {
    }

    public function create(Id $userId): UserPasswordRequest
    {
        return new UserPasswordRequest(
            $this->passwordUserRequestRepository->getNextIdentity(),
            $this->userRepository->getReference($userId),
            UserPasswordRequestCode::generate(),
            $this->datetimeService->getCurrentDatetime()
        );
    }
}
