<?php

declare(strict_types=1);


namespace App\EconumoBundle\Infrastructure\Doctrine\Repository\Traits;

use Doctrine\ORM\Exception\ORMException;
use App\EconumoBundle\Domain\Entity\ValueObject\Id;

trait GetEntityReferenceTrait
{
    /**
     * @throws ORMException
     */
    public function getEntityReference(string $entityName, Id | array $id): object|string|null
    {
        return $this->getEntityManager()->getReference($entityName, $id);
    }
}
