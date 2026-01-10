<?php

declare(strict_types=1);

namespace App\EconumoBundle\Domain\Repository;

use App\EconumoBundle\Domain\Entity\AccountOptions;
use App\EconumoBundle\Domain\Entity\ValueObject\Id;
use App\EconumoBundle\Domain\Exception\NotFoundException;

interface AccountOptionsRepositoryInterface
{
    /**
     * @return AccountOptions[]
     */
    public function getByUserId(Id $userId): array;

    /**
     * @throws NotFoundException
     */
    public function get(Id $accountId, Id $userId): AccountOptions;

    public function delete(AccountOptions $options): void;

    /**
     * @param AccountOptions[] $accountOptions
     */
    public function save(array $accountOptions): void;
}
