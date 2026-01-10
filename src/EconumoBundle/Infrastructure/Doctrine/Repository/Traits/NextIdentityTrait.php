<?php

declare(strict_types=1);

namespace App\EconumoBundle\Infrastructure\Doctrine\Repository\Traits;

use App\EconumoBundle\Domain\Entity\ValueObject\Id;
use Ramsey\Uuid\Uuid;

trait NextIdentityTrait
{
    public function getNextIdentity(): Id
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        $uuid = Uuid::uuid7();

        return new Id($uuid->toString());
    }
}
