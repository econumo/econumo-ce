<?php

declare(strict_types=1);

namespace App\EconumoBundle\Domain\Factory;

use App\EconumoBundle\Domain\Entity\User;
use App\EconumoBundle\Domain\Entity\UserOption;
use App\EconumoBundle\Domain\Repository\UserOptionRepositoryInterface;
use App\EconumoBundle\Domain\Service\DatetimeServiceInterface;
use App\EconumoBundle\Domain\Factory\UserOptionFactoryInterface;

class UserOptionFactory implements UserOptionFactoryInterface
{
    public function __construct(private readonly UserOptionRepositoryInterface $userOptionRepository, private readonly DatetimeServiceInterface $datetimeService)
    {
    }

    public function create(User $user, string $name, ?string $value): UserOption
    {
        return new UserOption(
            $this->userOptionRepository->getNextIdentity(),
            $user,
            $name,
            $value,
            $this->datetimeService->getCurrentDatetime()
        );
    }
}
