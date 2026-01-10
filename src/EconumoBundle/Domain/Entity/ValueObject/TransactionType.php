<?php

declare(strict_types=1);

namespace App\EconumoBundle\Domain\Entity\ValueObject;

use Stringable;
use DomainException;
use JsonSerializable;

final class TransactionType implements JsonSerializable, Stringable
{
    /**
     * @var int
     */
    public const EXPENSE = 0;

    /**
     * @var int
     */
    public const INCOME = 1;

    /**
     * @var int
     */
    public const TRANSFER = 2;

    /**
     * @var string
     */
    public const EXPENSE_ALIAS = 'expense';

    /**
     * @var string
     */
    public const INCOME_ALIAS = 'income';

    /**
     * @var string
     */
    public const TRANSFER_ALIAS = 'transfer';

    /**
     * @var array<string, int>
     */
    private const MAPPING = [
        self::EXPENSE_ALIAS => self::EXPENSE,
        self::INCOME_ALIAS => self::INCOME,
        self::TRANSFER_ALIAS => self::TRANSFER,
    ];

    private int $value;

    public static function createFromAlias(string $alias): self
    {
        $alias = strtolower(trim($alias));
        if (!array_key_exists($alias, self::MAPPING)) {
            throw new DomainException(sprintf('TransactionType %d not exists', $alias));
        }

        return new self(self::MAPPING[$alias]);
    }

    public function __construct(int $value)
    {
        if (!self::isValid($value)) {
            throw new DomainException(sprintf('TransactionType %d not exists', $value));
        }

        $this->value = $value;
    }

    public static function isValid(int $value): bool
    {
        return in_array($value, self::MAPPING, true);
    }

    public function getAlias(): string
    {
        $index = array_search($this->value, self::MAPPING, true);
        if (!empty($index)) {
            return $index;
        }

        throw new DomainException(sprintf('Alias for TransactionType %d not exists', $this->value));
    }

    public function isIncome(): bool
    {
        return $this->value === self::INCOME;
    }

    public function isExpense(): bool
    {
        return $this->value === self::EXPENSE;
    }

    public function isTransfer(): bool
    {
        return $this->value === self::TRANSFER;
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
