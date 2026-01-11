<?php

declare(strict_types=1);

namespace App\EconumoBundle\Domain\Events;

use App\EconumoBundle\Domain\Entity\ValueObject\Id;

final readonly class RemindPasswordRequestedEvent
{
    public function __construct(private Id $id)
    {
    }

    public function getId(): Id
    {
        return $this->id;
    }
}
