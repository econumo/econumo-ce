<?php

declare(strict_types=1);

namespace App\EconumoBundle\Domain\Service;

use DateTimeInterface;

interface DatetimeServiceInterface
{
    public function getCurrentDatetime(): DateTimeInterface;

    public function getNextDay(): DateTimeInterface;

    public function getCurrentMonthStart(): DateTimeInterface;

    public function getNextMonthStart(): DateTimeInterface;
}
