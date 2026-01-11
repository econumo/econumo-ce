<?php

declare(strict_types=1);

namespace App\EconumoBundle\Domain\Entity\ValueObject;

use Stringable;
use DomainException;
use JsonSerializable;

final class AccountType implements JsonSerializable, Stringable
{
    /**
     * @var int
     */
    public const CASH = 1;

    /**
     * @var int
     */
    public const CREDIT_CARD = 2;

    private int $value;

    public function __construct(int $value)
    {
        if (!self::isValid($value)) {
            throw new DomainException(sprintf('AccountType %d not exists', $value));
        }

        $this->value = $value;
    }

    public static function isValid(int $value): bool
    {
        return in_array($value, [self::CASH, self::CREDIT_CARD], true);
    }

    /**
     * @inheritdoc
     */
    public function jsonSerialize(): mixed
    {
        return $this->value;
    }

    public function isEqual(self $valueObject): bool
    {
        return $this->value === $valueObject->getValue();
    }

    public function getValue(): int
    {
        return $this->value;
    }

    public function __toString(): string
    {
        return (string)$this->value;
    }
}
