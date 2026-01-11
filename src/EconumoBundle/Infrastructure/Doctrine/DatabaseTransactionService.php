<?php

declare(strict_types=1);

namespace App\EconumoBundle\Infrastructure\Doctrine;

use App\EconumoBundle\Domain\Service\AntiCorruptionServiceInterface;
use Doctrine\ORM\EntityManagerInterface;

class DatabaseTransactionService implements AntiCorruptionServiceInterface
{
    private ?string $activeTransaction = null;

    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

    public function beginTransaction(string $name): void
    {
        if (null !== $this->activeTransaction) {
            return;
        }

        $this->entityManager->beginTransaction();
        $this->activeTransaction = $name;
    }

    public function commit(string $name): void
    {
        if ($this->activeTransaction !== $name) {
            return;
        }

        $this->entityManager->commit();
        $this->activeTransaction = null;
    }

    public function rollback(string $name): void
    {
        $this->entityManager->rollback();
        $this->activeTransaction = null;
    }
}
