<?php

declare(strict_types=1);


namespace App\EconumoBundle\Domain\Factory;


use App\EconumoBundle\Domain\Entity\Payee;
use App\EconumoBundle\Domain\Entity\ValueObject\Id;
use App\EconumoBundle\Domain\Entity\ValueObject\PayeeName;
use App\EconumoBundle\Domain\Repository\PayeeRepositoryInterface;
use App\EconumoBundle\Domain\Repository\UserRepositoryInterface;
use App\EconumoBundle\Domain\Service\DatetimeServiceInterface;
use App\EconumoBundle\Domain\Factory\PayeeFactoryInterface;

class PayeeFactory implements PayeeFactoryInterface
{
    public function __construct(private readonly DatetimeServiceInterface $datetimeService, private readonly PayeeRepositoryInterface $payeeRepository, private readonly UserRepositoryInterface $userRepository)
    {
    }

    public function create(Id $userId, PayeeName $name): Payee
    {
        return new Payee(
            $this->payeeRepository->getNextIdentity(),
            $this->userRepository->getReference($userId),
            $name,
            $this->datetimeService->getCurrentDatetime()
        );
    }
}
