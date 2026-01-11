<?php

declare(strict_types=1);

namespace App\EconumoBundle\UI\Service\Dto;

use App\EconumoBundle\Domain\Entity\ValueObject\Id;
use Symfony\Component\Lock\LockInterface;

class OperationDto
{
    public Id $operationId;

    public ?LockInterface $lock = null;
}
