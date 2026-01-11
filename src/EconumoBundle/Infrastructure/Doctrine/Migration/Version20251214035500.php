<?php

declare(strict_types=1);

namespace App\EconumoBundle\Infrastructure\Doctrine\Migration;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Migration to remove duplicate budget element limits and add unique constraint
 */
final class Version20251214035500 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Remove duplicate budget element limits and add unique constraint on (element_id, period)';
    }

    public function up(Schema $schema) : void
    {
        $platform = $this->connection->getDatabasePlatform()->getName();

        if ($platform === 'sqlite') {
            $this->upSqlite();
        } elseif ($platform === 'postgresql') {
            $this->upPostgresql();
        }
    }

    public function down(Schema $schema) : void
    {
        $platform = $this->connection->getDatabasePlatform()->getName();

        if ($platform === 'sqlite') {
            $this->downSqlite();
        } elseif ($platform === 'postgresql') {
            $this->downPostgresql();
        }
    }

    private function upSqlite(): void
    {
        // For SQLite, we need to recreate the table with a unique constraint
        // First, delete duplicates keeping only the most recent one (by id)
        $this->addSql('
            DELETE FROM budgets_elements_limits
            WHERE id NOT IN (
                SELECT MAX(id)
                FROM budgets_elements_limits
                GROUP BY element_id, period
            )
        ');

        // Recreate table with unique constraint
        $this->addSql('DROP INDEX element_period_idx_budgets_elements_limits');
        $this->addSql('DROP INDEX period_idx_budgets_elements_limits');
        $this->addSql('DROP INDEX IDX_406C516F1F1F2A24');
        $this->addSql('CREATE TEMPORARY TABLE __temp__budgets_elements_limits AS SELECT id, element_id, period, amount, created_at, updated_at FROM budgets_elements_limits');
        $this->addSql('DROP TABLE budgets_elements_limits');
        $this->addSql('CREATE TABLE budgets_elements_limits (
            id CHAR(36) NOT NULL COLLATE BINARY,
            element_id CHAR(36) NOT NULL COLLATE BINARY,
            period DATETIME NOT NULL,
            created_at DATETIME NOT NULL,
            updated_at DATETIME NOT NULL,
            amount NUMERIC(19, 8) NOT NULL,
            PRIMARY KEY(id),
            CONSTRAINT FK_406C516F1F1F2A24 FOREIGN KEY (element_id) REFERENCES budgets_elements (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE
        )');
        $this->addSql('INSERT INTO budgets_elements_limits (id, element_id, period, amount, created_at, updated_at) SELECT id, element_id, period, amount, created_at, updated_at FROM __temp__budgets_elements_limits');
        $this->addSql('DROP TABLE __temp__budgets_elements_limits');
        $this->addSql('CREATE INDEX period_idx_budgets_elements_limits ON budgets_elements_limits (period)');
        $this->addSql('CREATE INDEX IDX_406C516F1F1F2A24 ON budgets_elements_limits (element_id)');
        $this->addSql('CREATE UNIQUE INDEX element_period_uniq_budgets_elements_limits ON budgets_elements_limits (element_id, period)');
    }

    private function upPostgresql(): void
    {
        // For PostgreSQL, delete duplicates keeping only one (using ctid for tie-breaking)
        $this->addSql('
            DELETE FROM budgets_elements_limits
            WHERE ctid NOT IN (
                SELECT MIN(ctid)
                FROM budgets_elements_limits
                GROUP BY element_id, period
            )
        ');

        // Drop the old index and create a unique constraint
        $this->addSql('DROP INDEX IF EXISTS element_period_idx_budgets_elements_limits');
        $this->addSql('CREATE UNIQUE INDEX element_period_uniq_budgets_elements_limits ON budgets_elements_limits (element_id, period)');
    }

    private function downSqlite(): void
    {
        // Revert to non-unique index
        $this->addSql('DROP INDEX element_period_uniq_budgets_elements_limits');
        $this->addSql('DROP INDEX period_idx_budgets_elements_limits');
        $this->addSql('DROP INDEX IDX_406C516F1F1F2A24');
        $this->addSql('CREATE TEMPORARY TABLE __temp__budgets_elements_limits AS SELECT id, element_id, period, amount, created_at, updated_at FROM budgets_elements_limits');
        $this->addSql('DROP TABLE budgets_elements_limits');
        $this->addSql('CREATE TABLE budgets_elements_limits (
            id CHAR(36) NOT NULL COLLATE BINARY,
            element_id CHAR(36) NOT NULL COLLATE BINARY,
            period DATETIME NOT NULL,
            created_at DATETIME NOT NULL,
            updated_at DATETIME NOT NULL,
            amount NUMERIC(19, 8) NOT NULL,
            PRIMARY KEY(id),
            CONSTRAINT FK_406C516F1F1F2A24 FOREIGN KEY (element_id) REFERENCES budgets_elements (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE
        )');
        $this->addSql('INSERT INTO budgets_elements_limits (id, element_id, period, amount, created_at, updated_at) SELECT id, element_id, period, amount, created_at, updated_at FROM __temp__budgets_elements_limits');
        $this->addSql('DROP TABLE __temp__budgets_elements_limits');
        $this->addSql('CREATE INDEX element_period_idx_budgets_elements_limits ON budgets_elements_limits (element_id, period)');
        $this->addSql('CREATE INDEX period_idx_budgets_elements_limits ON budgets_elements_limits (period)');
        $this->addSql('CREATE INDEX IDX_406C516F1F1F2A24 ON budgets_elements_limits (element_id)');
    }

    private function downPostgresql(): void
    {
        // Revert unique index to regular index
        $this->addSql('DROP INDEX IF EXISTS element_period_uniq_budgets_elements_limits');
        $this->addSql('CREATE INDEX element_period_idx_budgets_elements_limits ON budgets_elements_limits (element_id, period)');
    }
}
