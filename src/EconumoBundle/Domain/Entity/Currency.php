<?php

declare(strict_types=1);

namespace App\EconumoBundle\Domain\Entity;

use App\EconumoBundle\Domain\Entity\ValueObject\CurrencyCode;
use App\EconumoBundle\Domain\Entity\ValueObject\DecimalNumber;
use App\EconumoBundle\Domain\Entity\ValueObject\Id;
use App\EconumoBundle\Domain\Exception\DomainException;
use App\EconumoBundle\Domain\Traits\EntityTrait;
use DateTimeImmutable;
use DateTimeInterface;
use Symfony\Component\Intl\Currencies;
use Symfony\Component\Intl\Exception\MissingResourceException;

class Currency
{
    use EntityTrait;

    /**
     * @var int
     */
    final public const DEFAULT_FRACTION_DIGITS = 2;

    private DateTimeImmutable $createdAt;

    public function __construct(
        private Id $id,
        private CurrencyCode $code,
        private string $symbol,
        private ?string $name,
        private int $fractionDigits,
        DateTimeInterface $createdAt
    ) {
        $this->createdAt = DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $createdAt->format('Y-m-d H:i:s'));
    }

    public function getId(): Id
    {
        return $this->id;
    }

    public function getCode(): CurrencyCode
    {
        return $this->code;
    }

    public function getSymbol(): string
    {
        return $this->symbol;
    }

    public function getFractionDigits(): int
    {
        return $this->fractionDigits;
    }

    public function getName(): string
    {
        if ($this->name !== null) {
            return $this->name;
        }

        try {
            return Currencies::getName($this->code->getValue());
        } catch (MissingResourceException) {
            return $this->code->getValue();
        }
    }

    public function updateName(?string $name): void
    {
        $this->name = $name;
    }

    public function updateSymbol(string $symbol): void
    {
        $this->symbol = $symbol;
    }

    public function updateFractionDigits(int $fractionDigits): void
    {
        if ($fractionDigits < 0 || $fractionDigits > DecimalNumber::SCALE) {
            throw new DomainException(sprintf('Fraction digits must be between 0 and %d', DecimalNumber::SCALE));
        }

        $this->fractionDigits = $fractionDigits;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }
}
