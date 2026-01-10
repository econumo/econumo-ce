<?php

declare(strict_types=1);

namespace App\EconumoBundle\Infrastructure\Doctrine\Factory;

use App\EconumoBundle\Domain\Entity\ValueObject\Id;
use App\EconumoBundle\Domain\Service\DatetimeServiceInterface;
use App\EconumoBundle\Infrastructure\Doctrine\Entity\OperationId;

class OperationIdFactory
{
    public function __construct(private readonly DatetimeServiceInterface $datetimeService)
    {
    }

    public function create(Id $id): OperationId
    {
        return new OperationId($id, $this->datetimeService->getCurrentDatetime());
    }
}
