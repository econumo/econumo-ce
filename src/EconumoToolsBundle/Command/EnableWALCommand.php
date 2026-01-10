<?php

declare(strict_types=1);

namespace App\EconumoToolsBundle\Command;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class EnableWALCommand extends Command
{
    protected static $defaultName = 'app:enable-wal';

    protected static $defaultDescription = 'Enable WAL-mode for SQLite';

    public function __construct(
        private readonly EntityManagerInterface $entityManager,
    ) {
        parent::__construct(self::$defaultName);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $connection = $this->entityManager->getConnection();
        $platform = $connection->getDatabasePlatform()->getName();

        if ($platform !== 'sqlite') {
            $io->warning(sprintf('WAL mode is only applicable to SQLite databases. Current database: %s', $platform));
            return Command::FAILURE;
        }

        $connection->executeStatement('PRAGMA journal_mode = WAL;');

        $io->success('WAL mode is enabled for SQLite');

        return Command::SUCCESS;
    }
}
