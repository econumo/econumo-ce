<?php

declare(strict_types=1);

namespace App\EconumoBundle\Infrastructure\Doctrine;

use Doctrine\DBAL\Event\ConnectionEventArgs;
use Doctrine\DBAL\Platforms\SqlitePlatform;

readonly class SQLiteConfiguration
{
    public function __construct(private int $busyTimeout = 0)
    {
    }

    public function postConnect(ConnectionEventArgs $args): void
    {
        $connection = $args->getConnection();
        if ($connection->getDatabasePlatform() instanceof SqlitePlatform) {
            $connection->executeStatement('PRAGMA foreign_keys = ON;');
            if ($this->busyTimeout > 0) {
                $connection->executeStatement(sprintf('PRAGMA busy_timeout = %d;', $this->busyTimeout));
            }
        }
    }
}
