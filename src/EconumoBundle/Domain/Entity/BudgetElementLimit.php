<?php

declare(strict_types=1);

namespace App\EconumoBundle\Domain\Entity;

use App\EconumoBundle\Domain\Entity\ValueObject\Id;
use App\EconumoBundle\Domain\Entity\ValueObject\DecimalNumber;
use DateTime;
use DateTimeImmutable;
use DateTimeInterface;

class BudgetElementLimit
{
    private DateTimeImmutable $period;

    private DateTimeImmutable $createdAt;

    private DateTimeInterface $updatedAt;

    public function __construct(
        private Id $id,
        private BudgetElement $element,
        private DecimalNumber $amount,
        DateTimeInterface $period,
        DateTimeInterface $createdAt
    ) {
        $this->period = DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $period->format('Y-m-01 00:00:00'));
        $this->createdAt = DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $createdAt->format('Y-m-d H:i:s'));
        $this->updatedAt = DateTime::createFromFormat('Y-m-d H:i:s', $createdAt->format('Y-m-d H:i:s'));
    }

    public function getId(): Id
    {
        return $this->id;
    }

    public function getElementId(): Id
    {
        return $this->element->getId();
    }

    public function getElement(): BudgetElement
    {
        return $this->element;
    }

    public function getAmount(): DecimalNumber
    {
        return $this->amount;
    }

    public function updateAmount(DecimalNumber $amount): void
    {
        if (!$this->amount->equals($amount)) {
            $this->amount = $amount;
            $this->updated();
        }
    }

    public function getPeriod(): DateTimeImmutable
    {
        return $this->period;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): DateTimeInterface
    {
        return $this->updatedAt;
    }

    private function updated(): void
    {
        $this->updatedAt = new DateTime();
    }
}