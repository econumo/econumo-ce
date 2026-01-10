<?php

declare(strict_types=1);

namespace App\EconumoBundle\UI\Service\Validator;

use App\EconumoBundle\Domain\Entity\ValueObject\ValueObjectInterface;
use Symfony\Component\Validator\Constraint;

interface ValueObjectValidationFactoryInterface
{
    public function create(string $valueObjectClassName): Constraint;
}
