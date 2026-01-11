<?php

declare(strict_types=1);

namespace App\EconumoToolsBundle\Command;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class MigrateSqliteToPostgresCommand extends Command
{
    protected static $defaultName = 'app:sqlite-to-postgres';

    protected static $defaultDescription = 'Migrate data from SQLite to PostgreSQL';

    private const TABLES_IN_ORDER = [
        // Order matters due to foreign key constraints
        // Base tables first
        'users',
        'users_options',
        'users_password_requests',
        'currencies',
        'currencies_rates',
        'folders',

        // Accounts and related tables
        'accounts',
        'accounts_options',
        'accounts_access',

        // Categories, Tags, Payees
        'categories',
        'tags',
        'payees',

        // Transactions (depends on accounts, categories, tags, payees)
        'transactions',

        // Budgets and related tables
        'budgets',
        'budgets_access',
        'budgets_folders',
        'budgets_envelopes',
        'budgets_elements',
        'budgets_elements_limits',

        // Connection invites
        'users_connections_invites',

        // Many-to-many join tables (must be last)
        'users_connections',
        'accounts_folders',
        'budgets_envelopes_categories',
        'budgets_excluded_accounts',
    ];

    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly ParameterBagInterface $params,
    ) {
        parent::__construct(self::$defaultName);
    }

    protected function configure(): void
    {
        $this
            ->addOption(
                'sqlite-path',
                's',
                InputOption::VALUE_REQUIRED,
                'Path to SQLite database file',
                null
            )
            ->addOption(
                'skip-tables',
                null,
                InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY,
                'Tables to skip during migration',
                []
            )
            ->addOption(
                'only-tables',
                null,
                InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY,
                'Only migrate specified tables (useful for testing)',
                []
            )
            ->addOption(
                'dry-run',
                'd',
                InputOption::VALUE_NONE,
                'Run migration without actually inserting data'
            )
            ->addOption(
                'no-truncate',
                null,
                InputOption::VALUE_NONE,
                'Do not truncate target tables before migration'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        // Validate current database is PostgreSQL
        $targetConnection = $this->entityManager->getConnection();
        $targetPlatform = $targetConnection->getDatabasePlatform()->getName();

        if ($targetPlatform !== 'postgresql') {
            $io->error(sprintf(
                'Target database must be PostgreSQL. Current database: %s',
                $targetPlatform
            ));
            $io->note('Switch to PostgreSQL using: ./bin/switch-database postgresql');
            return Command::FAILURE;
        }

        // Get SQLite database path
        $sqlitePath = $input->getOption('sqlite-path');
        if ($sqlitePath === null) {
            // Try to get from default location
            $sqlitePath = $this->params->get('kernel.project_dir') . '/var/db/db.sqlite';
        }

        if (!file_exists($sqlitePath)) {
            $io->error(sprintf('SQLite database not found at: %s', $sqlitePath));
            $io->note('Specify custom path with --sqlite-path option');
            return Command::FAILURE;
        }

        $io->title('SQLite to PostgreSQL Data Migration');
        $io->writeln(sprintf('Source (SQLite): %s', $sqlitePath));
        $io->writeln(sprintf('Target (PostgreSQL): %s', $this->getDatabaseName($targetConnection)));

        if ($input->getOption('dry-run')) {
            $io->warning('DRY RUN MODE - No data will be inserted');
        }

        if ($input->getOption('no-truncate')) {
            $io->warning('NO TRUNCATE MODE - Existing data will NOT be removed');
        }

        $onlyTables = $input->getOption('only-tables');
        if (!empty($onlyTables)) {
            $io->note(sprintf('Only migrating tables: %s', implode(', ', $onlyTables)));
        }

        if (!$io->confirm('Do you want to proceed with the migration?', false)) {
            $io->note('Migration cancelled');
            return Command::SUCCESS;
        }

        try {
            // Create SQLite connection
            $sourceConnection = DriverManager::getConnection([
                'url' => sprintf('sqlite:///%s', $sqlitePath),
            ]);

            $io->writeln('');
            $io->writeln('Testing connections...');

            // Test connections
            $sourceConnection->executeQuery('SELECT 1');
            $targetConnection->executeQuery('SELECT 1');

            $io->success('Both database connections are working');

            // Perform migration
            $this->migrateData(
                $sourceConnection,
                $targetConnection,
                $io,
                $output,
                $input->getOption('skip-tables'),
                $onlyTables,
                (bool)$input->getOption('dry-run'),
                (bool)$input->getOption('no-truncate')
            );

            $sourceConnection->close();

            $io->newLine();
            $io->success('Migration completed successfully!');

            if (!$input->getOption('dry-run')) {
                $io->note([
                    'Next steps:',
                    '1. Verify the data integrity',
                    '2. Run tests: task test',
                    '3. Test the application functionality',
                ]);
            }

            return Command::SUCCESS;

        } catch (\Throwable $e) {
            $io->error('Migration failed: ' . $e->getMessage());
            if ($output->isVerbose()) {
                $io->writeln($e->getTraceAsString());
            }
            return Command::FAILURE;
        }
    }

    private function migrateData(
        Connection $source,
        Connection $target,
        SymfonyStyle $io,
        OutputInterface $output,
        array $skipTables,
        array $onlyTables,
        bool $dryRun,
        bool $noTruncate
    ): void {
        // Disable foreign key checks temporarily
        if (!$dryRun) {
            $io->writeln('Disabling foreign key checks...');
            $target->executeStatement('SET session_replication_role = replica;');
        }

        try {
            $tablesToMigrate = self::TABLES_IN_ORDER;

            if (!empty($onlyTables)) {
                // Preserve order but only include specified tables
                $tablesToMigrate = array_values(array_intersect(self::TABLES_IN_ORDER, $onlyTables));
            }

            foreach ($tablesToMigrate as $tableName) {
                if (in_array($tableName, $skipTables, true)) {
                    $io->note(sprintf('Skipping table: %s', $tableName));
                    continue;
                }

                $this->migrateTable(
                    $source,
                    $target,
                    $tableName,
                    $io,
                    $output,
                    $dryRun,
                    $noTruncate
                );
            }
        } finally {
            // Re-enable foreign key checks
            if (!$dryRun) {
                $io->writeln('Re-enabling foreign key checks...');
                $target->executeStatement('SET session_replication_role = DEFAULT;');
            }
        }
    }

    private function migrateTable(
        Connection $source,
        Connection $target,
        string $tableName,
        SymfonyStyle $io,
        OutputInterface $output,
        bool $dryRun,
        bool $noTruncate
    ): void {
        $io->section(sprintf('Migrating table: %s', $tableName));

        // Check if table exists in source
        if (!$this->tableExists($source, $tableName)) {
            $io->warning(sprintf('Table %s does not exist in source database', $tableName));
            return;
        }

        // Check if table exists in target
        if (!$this->tableExists($target, $tableName)) {
            $io->warning(sprintf('Table %s does not exist in target database', $tableName));
            return;
        }

        // Get row count
        $count = (int)$source->fetchOne(sprintf('SELECT COUNT(*) FROM %s', $tableName));

        if ($count === 0) {
            $io->writeln('Table is empty, skipping...');
            return;
        }

        $io->writeln(sprintf('Found %d rows', $count));

        // Clear target table
        if (!$dryRun && !$noTruncate) {
            $io->writeln('Truncating target table...');
            $target->executeStatement(sprintf('TRUNCATE TABLE %s CASCADE', $tableName));
        }

        // Progress bar
        $progressBar = new ProgressBar($output, $count);
        $progressBar->setFormat('verbose');
        $progressBar->start();

        // Stream data from SQLite using a cursor to avoid loading all rows into memory
        $batchSize = 500;
        $batch = [];

        // Execute query and fetch rows one by one
        $result = $source->executeQuery(sprintf('SELECT * FROM %s', $tableName));

        while ($row = $result->fetchAssociative()) {
            $batch[] = $row;

            if (count($batch) >= $batchSize) {
                if (!$dryRun) {
                    $this->insertBatch($target, $tableName, $batch);
                }
                $progressBar->advance(count($batch));
                $batch = [];
            }
        }

        $result->free();

        // Insert remaining rows
        if (!empty($batch)) {
            if (!$dryRun) {
                $this->insertBatch($target, $tableName, $batch);
            }
            $progressBar->advance(count($batch));
        }

        $progressBar->finish();
        $io->newLine(2);

        // Reset sequences for PostgreSQL
        if (!$dryRun) {
            $this->resetSequences($target, $tableName, $io);
        }

        $io->success(sprintf('Migrated %d rows', $count));
    }

    private function insertBatch(Connection $connection, string $tableName, array $batch): void
    {
        if (empty($batch)) {
            return;
        }

        $columns = array_keys($batch[0]);
        $placeholders = [];
        $values = [];

        foreach ($batch as $row) {
            $rowPlaceholders = [];
            foreach ($columns as $column) {
                $rowPlaceholders[] = '?';
                $values[] = $row[$column];
            }
            $placeholders[] = '(' . implode(', ', $rowPlaceholders) . ')';
        }

        $sql = sprintf(
            'INSERT INTO %s (%s) VALUES %s',
            $tableName,
            implode(', ', $columns),
            implode(', ', $placeholders)
        );

        $connection->executeStatement($sql, $values);
    }

    private function resetSequences(Connection $connection, string $tableName, SymfonyStyle $io): void
    {
        try {
            // Find columns with sequences
            $sql = "
                SELECT column_name, column_default
                FROM information_schema.columns
                WHERE table_name = ?
                AND column_default LIKE 'nextval%'
            ";

            $columns = $connection->fetchAllAssociative($sql, [$tableName]);

            foreach ($columns as $column) {
                $columnName = $column['column_name'];

                // Extract sequence name from default value
                // Format: nextval('sequence_name'::regclass)
                if (preg_match("/nextval\('([^']+)'/", $column['column_default'], $matches)) {
                    $sequenceName = $matches[1];

                    // Reset sequence to max value
                    $connection->executeStatement(
                        sprintf(
                            "SELECT setval('%s', COALESCE((SELECT MAX(%s) FROM %s), 1), true)",
                            $sequenceName,
                            $columnName,
                            $tableName
                        )
                    );

                    if ($io->isVerbose()) {
                        $io->writeln(sprintf('Reset sequence: %s', $sequenceName));
                    }
                }
            }
        } catch (\Throwable $e) {
            $io->note(sprintf('Could not reset sequences for %s: %s', $tableName, $e->getMessage()));
        }
    }

    private function tableExists(Connection $connection, string $tableName): bool
    {
        $platform = $connection->getDatabasePlatform()->getName();

        if ($platform === 'sqlite') {
            $result = $connection->fetchOne(
                "SELECT name FROM sqlite_master WHERE type='table' AND name=?",
                [$tableName]
            );
        } else {
            $result = $connection->fetchOne(
                "SELECT tablename FROM pg_tables WHERE schemaname = 'public' AND tablename=?",
                [$tableName]
            );
        }

        return $result !== false;
    }

    private function getDatabaseName(Connection $connection): string
    {
        return (string)$connection->getDatabase();
    }
}
