<?php

declare(strict_types=1);

namespace App\EconumoBundle\Domain\Entity;

use App\EconumoBundle\Domain\Entity\Currency;
use App\EconumoBundle\Domain\Entity\ValueObject\Id;
use App\EconumoBundle\Domain\Entity\ValueObject\DecimalNumber;
use App\EconumoBundle\Domain\Traits\EntityTrait;
use DateTimeImmutable;
use DateTimeInterface;

class CurrencyRate
{
    use EntityTrait;

    private DateTimeImmutable $publishedAt;

    public function __construct(
        private Id $id,
        private Currency $currency,
        private Currency $baseCurrency,
        private DecimalNumber $rate,
        DateTimeInterface $createdAt
    ) {
        $this->publishedAt = DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $createdAt->format('Y-m-d') . ' 00:00:00');
    }

    public function getRate(): DecimalNumber
    {
        return $this->rate;
    }

    public function updateRate(DecimalNumber $rate): void
    {
        $this->rate = $rate;
    }

    public function getCurrency(): Currency
    {
        return $this->currency;
    }

    public function getBaseCurrency(): Currency
    {
        return $this->baseCurrency;
    }

    public function getPublishedAt(): DateTimeImmutable
    {
        return $this->publishedAt;
    }
}
