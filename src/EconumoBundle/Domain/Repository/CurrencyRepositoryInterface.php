<?php

declare(strict_types=1);

namespace App\EconumoBundle\Domain\Repository;

use App\EconumoBundle\Domain\Entity\Currency;
use App\EconumoBundle\Domain\Entity\ValueObject\CurrencyCode;
use App\EconumoBundle\Domain\Entity\ValueObject\Id;
use App\EconumoBundle\Domain\Exception\NotFoundException;

interface CurrencyRepositoryInterface
{
    public function getNextIdentity(): Id;

    /**
     * @throws NotFoundException
     */
    public function get(Id $id): Currency;

    /**
     * @return Currency[]
     */
    public function getByIds(array $ids): array;

    public function getByCode(CurrencyCode $code): ?Currency;

    /**
     * @return Currency[]
     */
    public function getAll(): array;

    public function getReference(Id $id): Currency;

    /**
     * @param Currency[] $items
     */
    public function save(array $items): void;
}
