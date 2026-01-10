<?php

declare(strict_types=1);


namespace App\EconumoBundle\Domain\Service\Analytics;

use App\EconumoBundle\Domain\Entity\ValueObject\Id;
use App\EconumoBundle\Domain\Service\Dto\BalanceAnalyticsDto;
use DateTimeInterface;

interface BalanceAnalyticsServiceInterface
{
    /**
     * @param DateTimeInterface $from
     * @param DateTimeInterface $to
     * @param Id $userId
     * @return BalanceAnalyticsDto[]
     */
    public function getBalanceAnalytics(DateTimeInterface $from, DateTimeInterface $to, Id $userId): array;
}
