<?php

declare(strict_types=1);

namespace App\EconumoBundle\Domain\Entity;

use App\EconumoBundle\Domain\Entity\ValueObject\AccountName;
use App\EconumoBundle\Domain\Entity\ValueObject\AccountType;
use App\EconumoBundle\Domain\Entity\ValueObject\CurrencyCode;
use App\EconumoBundle\Domain\Entity\ValueObject\Icon;
use App\EconumoBundle\Domain\Entity\ValueObject\Id;
use App\EconumoBundle\Domain\Traits\EntityTrait;
use DateTime;
use DateTimeImmutable;
use DateTimeInterface;

class Account
{
    use EntityTrait;

    private bool $isDeleted = false;

    private DateTimeImmutable $createdAt;

    private DateTimeInterface $updatedAt;

    public function __construct(
        private Id $id,
        private User $user,
        private AccountName $name,
        private Currency $currency,
        private AccountType $type,
        private Icon $icon,
        DateTimeInterface $createdAt
    ) {
        $this->createdAt = DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $createdAt->format('Y-m-d H:i:s'));
        $this->updatedAt = DateTime::createFromFormat('Y-m-d H:i:s', $createdAt->format('Y-m-d H:i:s'));
    }

    public function getId(): Id
    {
        return $this->id;
    }

    public function getUserId(): Id
    {
        return $this->user->getId();
    }

    public function getName(): AccountName
    {
        return $this->name;
    }

    public function getCurrency(): Currency
    {
        return $this->currency;
    }

    public function getCurrencyId(): Id
    {
        return $this->currency->getId();
    }

    public function getCurrencyCode(): CurrencyCode
    {
        return $this->currency->getCode();
    }

    public function getType(): AccountType
    {
        return $this->type;
    }

    public function getIcon(): Icon
    {
        return $this->icon;
    }

    public function updateName(AccountName $name): void
    {
        if (!$this->name->isEqual($name)) {
            $this->name = $name;
            $this->updated();
        }
    }

    public function updateIcon(Icon $icon): void
    {
        if (!$this->icon->isEqual($icon)) {
            $this->icon = $icon;
            $this->updated();
        }
    }

    public function updateCurrency(Currency $currency): void
    {
        if (!$this->currency->getId()->isEqual($currency->getId())) {
            $this->currency = $currency;
            $this->updated();
        }
    }

    public function isDeleted(): bool
    {
        return $this->isDeleted;
    }

    public function delete(): void
    {
        if (!$this->isDeleted) {
            $this->isDeleted = true;
            $this->updated();
        }
    }

    public function getCreatedAt(): DateTimeInterface
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
