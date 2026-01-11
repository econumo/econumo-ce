<?php

declare(strict_types=1);

namespace App\EconumoBundle\Domain\Entity\ValueObject;

interface ValueObjectInterface
{
    public static function validate($value): void;

    public function getValue();

    public function isEqual(ValueObjectInterface $valueObject): bool;

    public function __toString(): string;
}
