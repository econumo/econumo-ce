<?php

declare(strict_types=1);

namespace App\EconumoBundle\Domain\Service;

interface MetricServiceInterface
{
    /**
     * Increase metrics value for 1
     */
    public function increment(string $metric): void;

    /**
     * Count metrics value
     */
    public function count(string $metric, int $count): void;

    /**
     * Set metrics value
     */
    public function gauge(string $metric, int $value): void;
}
