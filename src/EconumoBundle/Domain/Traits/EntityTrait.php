<?php

declare(strict_types=1);


namespace App\EconumoBundle\Domain\Traits;

trait EntityTrait
{
    public function __toString(): string
    {
        return self::class;
    }
}
