<?php

declare(strict_types=1);

namespace App\EconumoBundle\Domain\Entity\ValueObject;

use Stringable;
use App\EconumoBundle\Domain\Entity\ValueObject\ValueObjectInterface;
use DomainException;
use JsonSerializable;

class Icon implements ValueObjectInterface, JsonSerializable, Stringable
{
    private string $value;

    public function __construct(string $value)
    {
        self::validate($value);
        $this->value = $value;
    }

    public function __toString(): string
    {
        return $this->value;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * @inheritdoc
     */
    public function jsonSerialize(): mixed
    {
        return $this->value;
    }

    public function isEqual(ValueObjectInterface $valueObject): bool
    {
        return $this->value === $valueObject->getValue();
    }

    public static function validate($value): void
    {
        if (empty($value)) {
            throw new DomainException('Icon value must not be empty');
        }
    }
}
