<?php

declare(strict_types=1);

namespace App\EconumoBundle\UI\Service;

use App\EconumoBundle\Domain\Entity\ValueObject\Id;
use App\EconumoBundle\UI\Service\Dto\OperationDto;

interface OperationServiceInterface
{
    public function lock(Id $id): OperationDto;

    public function release(OperationDto $dto): void;
}
