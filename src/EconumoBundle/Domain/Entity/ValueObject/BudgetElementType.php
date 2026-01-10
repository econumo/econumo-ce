<?php

declare(strict_types=1);

namespace App\EconumoBundle\Domain\Entity\ValueObject;

use Stringable;
use App\EconumoBundle\Domain\Entity\ValueObject\ValueObjectInterface;
use DomainException;
use JsonSerializable;

final class BudgetElementType implements JsonSerializable, ValueObjectInterface, Stringable
{
    /**
     * @var int
     */
    public const ENVELOPE = 0;

    /**
     * @var string
     */
    public const ENVELOPE_ALIAS = 'envelope';

    /**
     * @var int
     */
    public const CATEGORY = 1;

    /**
     * @var string
     */
    public const CATEGORY_ALIAS = 'category';

    /**
     * @var int
     */
    public const TAG = 2;

    /**
     * @var string
     */
    public const TAG_ALIAS = 'tag';

    /**
     * @var string[]
     */
    public const MAPPING = [
        self::ENVELOPE => self::ENVELOPE_ALIAS,
        self::CATEGORY => self::CATEGORY_ALIAS,
        self::TAG => self::TAG_ALIAS,
    ];

    private int $value;

    public static function envelope(): self
    {
        return new self(self::ENVELOPE);
    }

    public static function category(): self
    {
        return new self(self::CATEGORY);
    }

    public static function tag(): self
    {
        return new self(self::TAG);
    }

    public static function createFromAlias(string $alias): self
    {
        $index = array_search($alias, self::MAPPING, true);
        if ($index === false) {
            throw new DomainException(sprintf('BudgetElementType with alias %d not exists', $alias));
        }

        return new self((int)$index);
    }

    public function __construct(int $value)
    {
        if (!self::isValid($value)) {
            throw new DomainException(sprintf('BudgetElementType %d not exists', $value));
        }

        $this->value = $value;
    }

    public function getAlias(): string
    {
        return self::MAPPING[$this->value];
    }

    public function isEnvelope(): bool
    {
        return $this->value === self::ENVELOPE;
    }

    public function isCategory(): bool
    {
        return $this->value === self::CATEGORY;
    }

    public function isTag(): bool
    {
        return $this->value === self::TAG;
    }

    public static function isValid(int $value): bool
    {
        return in_array($value, [self::ENVELOPE, self::CATEGORY, self::TAG], true);
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
        if (!is_numeric($value)) {
            throw new DomainException(sprintf('BudgetElementType %d is not numeric', $value));
        }

        new self((int)$value);
    }
}
