<?php

declare(strict_types=1);

namespace App\EconumoBundle\Infrastructure\Datetime;

use App\EconumoBundle\Domain\Service\DatetimeServiceInterface;
use DateTime;
use DateTimeImmutable;
use DateTimeInterface;

class DatetimeService implements DatetimeServiceInterface
{
    /**
     * @inheritDoc
     */
    public function getCurrentDatetime(): DateTimeInterface
    {
        return new DateTimeImmutable();
    }

    public function getNextDay(): DateTimeInterface
    {
        $now = new DateTime();
        $now->setTime(0, 0, 0, 0);
        $now->modify('+1 day');
        return DateTimeImmutable::createFromMutable($now);
    }

    public function getCurrentMonthStart(): DateTimeInterface
    {
        $now = new DateTime();
        return DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $now->format('Y-m-01 00:00:00'));
    }

    public function getNextMonthStart(): DateTimeInterface
    {
        $now = new DateTime();
        $now->modify('next month');
        return DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $now->format('Y-m-01 00:00:00'));
    }
}
