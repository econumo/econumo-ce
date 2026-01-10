<?php

declare(strict_types=1);

namespace App\EconumoBundle\Domain\Entity;

use App\EconumoBundle\Domain\Entity\ValueObject\BudgetElementType;
use App\EconumoBundle\Domain\Entity\ValueObject\Id;
use DateTime;
use DateTimeImmutable;
use DateTimeInterface;

class BudgetElement
{
    /**
     * @var int
     */
    final public const POSITION_UNSET = 0;

    private DateTimeImmutable $createdAt;

    private DateTimeInterface $updatedAt;

    public function __construct(
        private Id $id,
        private Budget $budget,
        private Id $externalId,
        private BudgetElementType $type,
        private ?Currency $currency,
        private ?BudgetFolder $folder,
        private int $position,
        DateTimeInterface $createdAt
    ) {
        $this->createdAt = DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $createdAt->format('Y-m-d H:i:s'));
        $this->updatedAt = DateTime::createFromFormat('Y-m-d H:i:s', $createdAt->format('Y-m-d H:i:s'));
    }

    public function getId(): Id
    {
        return $this->id;
    }

    public function getBudget(): Budget
    {
        return $this->budget;
    }

    public function getCurrency(): ?Currency
    {
        return $this->currency;
    }

    public function getFolder(): ?BudgetFolder
    {
        return $this->folder;
    }

    public function changeFolder(?BudgetFolder $budgetFolder): void
    {
        $this->folder = $budgetFolder;
    }

    public function getExternalId(): Id
    {
        return $this->externalId;
    }

    public function getType(): BudgetElementType
    {
        return $this->type;
    }

    public function isPositionUnset(): bool
    {
        return self::POSITION_UNSET === $this->position;
    }

    public function getPosition(): int
    {
        return $this->position;
    }

    public function updatePosition(int $position): void
    {
        if ($this->position !== $position) {
            $this->position = $position;
            $this->updated();
        }
    }

    public function unsetPosition(): void
    {
        if ($this->position !== self::POSITION_UNSET) {
            $this->position = self::POSITION_UNSET;
            $this->updated();
        }
    }

    public function updateCurrency(?Currency $currency): void
    {
        if (($this->currency instanceof Currency && $currency instanceof Currency && !$this->currency->getId()->isEqual($currency->getId()))
            || (!$this->currency instanceof Currency && $currency instanceof Currency)
            || ($this->currency instanceof Currency && !$currency instanceof Currency)) {
            $this->currency = $currency;
            $this->updated();
        }
    }

    private function updated(): void
    {
        $this->updatedAt = new DateTime();
    }
}