<?php

declare(strict_types=1);

namespace App\EconumoBundle\Domain\Entity\ValueObject;

use Stringable;
use DomainException;
use JsonSerializable;

final class BudgetUserRole implements JsonSerializable, ValueObjectInterface, Stringable
{
    /**
     * @var int
     */
    public const ADMIN = 0;

    /**
     * @var int
     */
    public const USER = 1;

    /**
     * @var int
     */
    public const GUEST = 2;

    /**
     * @var string[]
     */
    public const MAPPING = [
        self::ADMIN => 'admin',
        self::USER => 'user',
        self::GUEST => 'guest',
    ];

    private int $value;

    public static function admin(): self
    {
        return new self(self::ADMIN);
    }

    public static function user(): self
    {
        return new self(self::USER);
    }

    public static function guest(): self
    {
        return new self(self::GUEST);
    }

    public static function createFromAlias(string $alias): self
    {
        $index = array_search($alias, self::MAPPING, true);
        if ($index === false) {
            throw new DomainException(sprintf('AccountRole with alias %d not exists', $alias));
        }

        return new self((int)$index);
    }

    public function __construct(int $value)
    {
        if (!self::isValid($value)) {
            throw new DomainException(sprintf('AccountRole %d not exists', $value));
        }

        $this->value = $value;
    }

    public function getAlias(): string
    {
        return self::MAPPING[$this->value];
    }

    public function isAdmin(): bool
    {
        return $this->value === self::ADMIN;
    }

    public function isUser(): bool
    {
        return $this->value === self::USER;
    }

    public function isGuest(): bool
    {
        return $this->value === self::GUEST;
    }

    public static function isValid(int $value): bool
    {
        return in_array($value, [self::ADMIN, self::USER, self::GUEST], true);
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

    public function getValue(): int
    {
        return $this->value;
    }

    public function __toString(): string
    {
        return (string)$this->value;
    }

    public static function validate($value): void
    {
        if (empty($value)) {
            throw new DomainException('Value cannot be empty');
        }

        self::createFromAlias($value);
    }
}
