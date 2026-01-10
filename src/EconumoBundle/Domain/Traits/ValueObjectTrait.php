<?php

declare(strict_types=1);


namespace App\EconumoBundle\Domain\Traits;

use App\EconumoBundle\Domain\Entity\ValueObject\ValueObjectInterface;

trait ValueObjectTrait
{
    protected string $value;

    public static function validate(string $value): void
    {
    }

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

    public function isEqual(ValueObjectInterface $valueObject): bool
    {
        return strcasecmp($this->value, $valueObject->getValue()) === 0;
    }

    /**
     * @inheritdoc
     */
    public function jsonSerialize(): mixed
    {
        return $this->value;
    }
}
