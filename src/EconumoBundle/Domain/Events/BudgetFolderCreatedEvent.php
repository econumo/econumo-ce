<?php

declare(strict_types=1);

namespace App\EconumoBundle\Domain\Events;

use App\EconumoBundle\Domain\Entity\ValueObject\Id;

final readonly class BudgetFolderCreatedEvent
{
    public function __construct(private Id $folderId)
    {
    }

    public function getFolderId(): Id
    {
        return $this->folderId;
    }
}
