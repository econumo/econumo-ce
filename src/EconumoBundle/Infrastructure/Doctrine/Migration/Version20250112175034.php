<?php

declare(strict_types=1);

namespace App\EconumoBundle\Infrastructure\Doctrine\Migration;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250112175034 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Change decimal precision from (19,2) to (19,8) for monetary amounts';
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
        // budgets_elements_limits
        $this->addSql('DROP INDEX element_period_idx_budgets_elements_limits');
        $this->addSql('DROP INDEX period_idx_budgets_elements_limits');
        $this->addSql('DROP INDEX IDX_406C516F1F1F2A24');
        $this->addSql('CREATE TEMPORARY TABLE __temp__budgets_elements_limits AS SELECT id, element_id, period, amount, created_at, updated_at FROM budgets_elements_limits');
        $this->addSql('DROP TABLE budgets_elements_limits');
        $this->addSql('CREATE TABLE budgets_elements_limits (id CHAR(36) NOT NULL COLLATE BINARY --(DC2Type:uuid)
        , element_id CHAR(36) NOT NULL COLLATE BINARY --(DC2Type:uuid)
        , period DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , created_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , updated_at DATETIME NOT NULL
        , amount NUMERIC(19, 8) NOT NULL
        , PRIMARY KEY(id)
        , CONSTRAINT FK_406C516F1F1F2A24 FOREIGN KEY (element_id) REFERENCES budgets_elements (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO budgets_elements_limits (id, element_id, period, amount, created_at, updated_at) SELECT id, element_id, period, amount, created_at, updated_at FROM __temp__budgets_elements_limits');
        $this->addSql('DROP TABLE __temp__budgets_elements_limits');
        $this->addSql('CREATE INDEX element_period_idx_budgets_elements_limits ON budgets_elements_limits (element_id, period)');
        $this->addSql('CREATE INDEX period_idx_budgets_elements_limits ON budgets_elements_limits (period)');
        $this->addSql('CREATE INDEX IDX_406C516F1F1F2A24 ON budgets_elements_limits (element_id)');

        // currencies_rates
        $this->addSql('DROP INDEX identifier_uniq_currencies_rates');
        $this->addSql('DROP INDEX base_currency_id_published_at_idx_currencies_rates');
        $this->addSql('DROP INDEX currency_id_published_at_idx_currencies_rates');
        $this->addSql('DROP INDEX published_at_idx_currencies_rates');
        $this->addSql('DROP INDEX IDX_5AA604E03101778E');
        $this->addSql('DROP INDEX IDX_5AA604E038248176');
        $this->addSql('CREATE TEMPORARY TABLE __temp__currencies_rates AS SELECT id, currency_id, base_currency_id, rate, published_at FROM currencies_rates');
        $this->addSql('DROP TABLE currencies_rates');
        $this->addSql('CREATE TABLE currencies_rates (id CHAR(36) NOT NULL COLLATE BINARY --(DC2Type:uuid)
        , currency_id CHAR(36) NOT NULL COLLATE BINARY --(DC2Type:uuid)
        , base_currency_id CHAR(36) NOT NULL COLLATE BINARY --(DC2Type:uuid)
        , published_at DATE NOT NULL --(DC2Type:date_immutable)
        , rate NUMERIC(19, 8) NOT NULL
        , PRIMARY KEY(id)
        , CONSTRAINT FK_5AA604E038248176 FOREIGN KEY (currency_id) REFERENCES currencies (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE
        , CONSTRAINT FK_5AA604E03101778E FOREIGN KEY (base_currency_id) REFERENCES currencies (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO currencies_rates (id, currency_id, base_currency_id, rate, published_at) SELECT id, currency_id, base_currency_id, rate, published_at FROM __temp__currencies_rates');
        $this->addSql('DROP TABLE __temp__currencies_rates');
        $this->addSql('CREATE UNIQUE INDEX identifier_uniq_currencies_rates ON currencies_rates (published_at, currency_id, base_currency_id)');
        $this->addSql('CREATE INDEX base_currency_id_published_at_idx_currencies_rates ON currencies_rates (base_currency_id, published_at)');
        $this->addSql('CREATE INDEX currency_id_published_at_idx_currencies_rates ON currencies_rates (currency_id, published_at)');
        $this->addSql('CREATE INDEX published_at_idx_currencies_rates ON currencies_rates (published_at)');
        $this->addSql('CREATE INDEX IDX_5AA604E03101778E ON currencies_rates (base_currency_id)');
        $this->addSql('CREATE INDEX IDX_5AA604E038248176 ON currencies_rates (currency_id)');

        // transactions
        $this->addSql('DROP INDEX tag_id_account_id_spent_at_idx_transactions');
        $this->addSql('DROP INDEX category_id_account_id_spent_at_idx_transactions');
        $this->addSql('DROP INDEX account_recipient_id_spent_at_idx_transactions');
        $this->addSql('DROP INDEX account_id_spent_at_idx_transactions');
        $this->addSql('DROP INDEX spent_idx_transactions');
        $this->addSql('DROP INDEX type_idx_transactions');
        $this->addSql('DROP INDEX IDX_EAA81A4CBAD26311');
        $this->addSql('DROP INDEX IDX_EAA81A4CCB4B68F');
        $this->addSql('DROP INDEX IDX_EAA81A4C12469DE2');
        $this->addSql('DROP INDEX IDX_EAA81A4C70F7993E');
        $this->addSql('DROP INDEX IDX_EAA81A4C9B6B5FBA');
        $this->addSql('DROP INDEX IDX_EAA81A4CA76ED395');
        $this->addSql('CREATE TEMPORARY TABLE __temp__transactions AS SELECT id, user_id, account_id, account_recipient_id, category_id, payee_id, tag_id, type, amount, amount_recipient, description, created_at, updated_at, spent_at FROM transactions');
        $this->addSql('DROP TABLE transactions');
        $this->addSql('CREATE TABLE transactions (id CHAR(36) NOT NULL COLLATE BINARY --(DC2Type:uuid)
        , user_id CHAR(36) NOT NULL COLLATE BINARY --(DC2Type:uuid)
        , account_id CHAR(36) NOT NULL COLLATE BINARY --(DC2Type:uuid)
        , account_recipient_id CHAR(36) DEFAULT NULL COLLATE BINARY --(DC2Type:uuid)
        , category_id CHAR(36) DEFAULT NULL COLLATE BINARY --(DC2Type:uuid)
        , payee_id CHAR(36) DEFAULT NULL COLLATE BINARY --(DC2Type:uuid)
        , tag_id CHAR(36) DEFAULT NULL COLLATE BINARY --(DC2Type:uuid)
        , description VARCHAR(255) NOT NULL COLLATE BINARY, created_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , updated_at DATETIME NOT NULL
        , spent_at DATETIME NOT NULL
        , type SMALLINT NOT NULL
        , amount NUMERIC(19, 8) NOT NULL
        , amount_recipient NUMERIC(19, 8) DEFAULT NULL
        , PRIMARY KEY(id)
        , CONSTRAINT FK_EAA81A4CA76ED395 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE
        , CONSTRAINT FK_EAA81A4C9B6B5FBA FOREIGN KEY (account_id) REFERENCES accounts (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE
        , CONSTRAINT FK_EAA81A4C70F7993E FOREIGN KEY (account_recipient_id) REFERENCES accounts (id) ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE
        , CONSTRAINT FK_EAA81A4C12469DE2 FOREIGN KEY (category_id) REFERENCES categories (id) ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE
        , CONSTRAINT FK_EAA81A4CCB4B68F FOREIGN KEY (payee_id) REFERENCES payees (id) ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE
        , CONSTRAINT FK_EAA81A4CBAD26311 FOREIGN KEY (tag_id) REFERENCES tags (id) ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO transactions (id, user_id, account_id, account_recipient_id, category_id, payee_id, tag_id, type, amount, amount_recipient, description, created_at, updated_at, spent_at) SELECT id, user_id, account_id, account_recipient_id, category_id, payee_id, tag_id, type, amount, amount_recipient, description, created_at, updated_at, spent_at FROM __temp__transactions');
        $this->addSql('DROP TABLE __temp__transactions');
        $this->addSql('CREATE INDEX tag_id_account_id_spent_at_idx_transactions ON transactions (tag_id, account_id, spent_at)');
        $this->addSql('CREATE INDEX category_id_account_id_spent_at_idx_transactions ON transactions (category_id, account_id, spent_at)');
        $this->addSql('CREATE INDEX account_recipient_id_spent_at_idx_transactions ON transactions (account_recipient_id, spent_at)');
        $this->addSql('CREATE INDEX account_id_spent_at_idx_transactions ON transactions (account_id, spent_at)');
        $this->addSql('CREATE INDEX spent_idx_transactions ON transactions (spent_at)');
        $this->addSql('CREATE INDEX type_idx_transactions ON transactions (type)');
        $this->addSql('CREATE INDEX IDX_EAA81A4CBAD26311 ON transactions (tag_id)');
        $this->addSql('CREATE INDEX IDX_EAA81A4CCB4B68F ON transactions (payee_id)');
        $this->addSql('CREATE INDEX IDX_EAA81A4C12469DE2 ON transactions (category_id)');
        $this->addSql('CREATE INDEX IDX_EAA81A4C70F7993E ON transactions (account_recipient_id)');
        $this->addSql('CREATE INDEX IDX_EAA81A4C9B6B5FBA ON transactions (account_id)');
        $this->addSql('CREATE INDEX IDX_EAA81A4CA76ED395 ON transactions (user_id)');
    }

    private function upPostgresql(): void
    {
        // PostgreSQL supports ALTER COLUMN TYPE directly
        $this->addSql('ALTER TABLE budgets_elements_limits ALTER COLUMN amount TYPE NUMERIC(19, 8)');
        $this->addSql('ALTER TABLE currencies_rates ALTER COLUMN rate TYPE NUMERIC(19, 8)');
        $this->addSql('ALTER TABLE transactions ALTER COLUMN amount TYPE NUMERIC(19, 8)');
        $this->addSql('ALTER TABLE transactions ALTER COLUMN amount_recipient TYPE NUMERIC(19, 8)');
    }

    private function downSqlite(): void
    {
        // Revert precision back to (19, 2)
        $this->addSql('DROP INDEX IDX_406C516F1F1F2A24');
        $this->addSql('DROP INDEX period_idx_budgets_elements_limits');
        $this->addSql('DROP INDEX element_period_idx_budgets_elements_limits');
        $this->addSql('CREATE TEMPORARY TABLE __temp__budgets_elements_limits AS SELECT id, element_id, period, amount, created_at, updated_at FROM budgets_elements_limits');
        $this->addSql('DROP TABLE budgets_elements_limits');
        $this->addSql('CREATE TABLE budgets_elements_limits (id CHAR(36) NOT NULL --(DC2Type:uuid)
        , element_id CHAR(36) NOT NULL --(DC2Type:uuid)
        , period DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , created_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , updated_at DATETIME NOT NULL
        , amount NUMERIC(19, 2) NOT NULL
        , PRIMARY KEY(id)
        , CONSTRAINT FK_406C516F1F1F2A24 FOREIGN KEY (element_id) REFERENCES budgets_elements (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO budgets_elements_limits (id, element_id, period, amount, created_at, updated_at) SELECT id, element_id, period, amount, created_at, updated_at FROM __temp__budgets_elements_limits');
        $this->addSql('DROP TABLE __temp__budgets_elements_limits');
        $this->addSql('CREATE INDEX IDX_406C516F1F1F2A24 ON budgets_elements_limits (element_id)');
        $this->addSql('CREATE INDEX period_idx_budgets_elements_limits ON budgets_elements_limits (period)');
        $this->addSql('CREATE INDEX element_period_idx_budgets_elements_limits ON budgets_elements_limits (element_id, period)');

        // currencies_rates
        $this->addSql('DROP INDEX IDX_5AA604E038248176');
        $this->addSql('DROP INDEX IDX_5AA604E03101778E');
        $this->addSql('DROP INDEX published_at_idx_currencies_rates');
        $this->addSql('DROP INDEX currency_id_published_at_idx_currencies_rates');
        $this->addSql('DROP INDEX base_currency_id_published_at_idx_currencies_rates');
        $this->addSql('DROP INDEX identifier_uniq_currencies_rates');
        $this->addSql('CREATE TEMPORARY TABLE __temp__currencies_rates AS SELECT id, currency_id, base_currency_id, rate, published_at FROM currencies_rates');
        $this->addSql('DROP TABLE currencies_rates');
        $this->addSql('CREATE TABLE currencies_rates (id CHAR(36) NOT NULL --(DC2Type:uuid)
        , currency_id CHAR(36) NOT NULL --(DC2Type:uuid)
        , base_currency_id CHAR(36) NOT NULL --(DC2Type:uuid)
        , published_at DATE NOT NULL --(DC2Type:date_immutable)
        , rate NUMERIC(16, 8) NOT NULL
        , PRIMARY KEY(id)
        , CONSTRAINT FK_5AA604E038248176 FOREIGN KEY (currency_id) REFERENCES currencies (id) ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE
        , CONSTRAINT FK_5AA604E03101778E FOREIGN KEY (base_currency_id) REFERENCES currencies (id) ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO currencies_rates (id, currency_id, base_currency_id, rate, published_at) SELECT id, currency_id, base_currency_id, rate, published_at FROM __temp__currencies_rates');
        $this->addSql('DROP TABLE __temp__currencies_rates');
        $this->addSql('CREATE UNIQUE INDEX identifier_uniq_currencies_rates ON currencies_rates (published_at, currency_id, base_currency_id)');
        $this->addSql('CREATE INDEX base_currency_id_published_at_idx_currencies_rates ON currencies_rates (base_currency_id, published_at)');
        $this->addSql('CREATE INDEX currency_id_published_at_idx_currencies_rates ON currencies_rates (currency_id, published_at)');
        $this->addSql('CREATE INDEX published_at_idx_currencies_rates ON currencies_rates (published_at)');
        $this->addSql('CREATE INDEX IDX_5AA604E03101778E ON currencies_rates (base_currency_id)');
        $this->addSql('CREATE INDEX IDX_5AA604E038248176 ON currencies_rates (currency_id)');

        // transactions
        $this->addSql('DROP INDEX IDX_EAA81A4CA76ED395');
        $this->addSql('DROP INDEX IDX_EAA81A4C9B6B5FBA');
        $this->addSql('DROP INDEX IDX_EAA81A4C70F7993E');
        $this->addSql('DROP INDEX IDX_EAA81A4C12469DE2');
        $this->addSql('DROP INDEX IDX_EAA81A4CCB4B68F');
        $this->addSql('DROP INDEX IDX_EAA81A4CBAD26311');
        $this->addSql('DROP INDEX type_idx_transactions');
        $this->addSql('DROP INDEX spent_idx_transactions');
        $this->addSql('DROP INDEX account_id_spent_at_idx_transactions');
        $this->addSql('DROP INDEX account_recipient_id_spent_at_idx_transactions');
        $this->addSql('DROP INDEX category_id_account_id_spent_at_idx_transactions');
        $this->addSql('DROP INDEX tag_id_account_id_spent_at_idx_transactions');
        $this->addSql('CREATE TEMPORARY TABLE __temp__transactions AS SELECT id, user_id, account_id, account_recipient_id, category_id, payee_id, tag_id, type, amount, amount_recipient, description, created_at, updated_at, spent_at FROM transactions');
        $this->addSql('DROP TABLE transactions');
        $this->addSql('CREATE TABLE transactions (id CHAR(36) NOT NULL --(DC2Type:uuid)
        , user_id CHAR(36) NOT NULL --(DC2Type:uuid)
        , account_id CHAR(36) NOT NULL --(DC2Type:uuid)
        , account_recipient_id CHAR(36) DEFAULT NULL --(DC2Type:uuid)
        , category_id CHAR(36) DEFAULT NULL --(DC2Type:uuid)
        , payee_id CHAR(36) DEFAULT NULL --(DC2Type:uuid)
        , tag_id CHAR(36) DEFAULT NULL --(DC2Type:uuid)
        , description VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , updated_at DATETIME NOT NULL
        , spent_at DATETIME NOT NULL
        , type SMALLINT NOT NULL
        , amount NUMERIC(19, 2) NOT NULL
        , amount_recipient NUMERIC(19, 2) DEFAULT NULL
        , PRIMARY KEY(id)
        , CONSTRAINT FK_EAA81A4CA76ED395 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE
        , CONSTRAINT FK_EAA81A4C9B6B5FBA FOREIGN KEY (account_id) REFERENCES accounts (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE
        , CONSTRAINT FK_EAA81A4C70F7993E FOREIGN KEY (account_recipient_id) REFERENCES accounts (id) ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE
        , CONSTRAINT FK_EAA81A4C12469DE2 FOREIGN KEY (category_id) REFERENCES categories (id) ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE
        , CONSTRAINT FK_EAA81A4CCB4B68F FOREIGN KEY (payee_id) REFERENCES payees (id) ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE
        , CONSTRAINT FK_EAA81A4CBAD26311 FOREIGN KEY (tag_id) REFERENCES tags (id) ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO transactions (id, user_id, account_id, account_recipient_id, category_id, payee_id, tag_id, type, amount, amount_recipient, description, created_at, updated_at, spent_at) SELECT id, user_id, account_id, account_recipient_id, category_id, payee_id, tag_id, type, amount, amount_recipient, description, created_at, updated_at, spent_at FROM __temp__transactions');
        $this->addSql('DROP TABLE __temp__transactions');
        $this->addSql('CREATE INDEX IDX_EAA81A4CA76ED395 ON transactions (user_id)');
        $this->addSql('CREATE INDEX IDX_EAA81A4C9B6B5FBA ON transactions (account_id)');
        $this->addSql('CREATE INDEX IDX_EAA81A4C70F7993E ON transactions (account_recipient_id)');
        $this->addSql('CREATE INDEX IDX_EAA81A4C12469DE2 ON transactions (category_id)');
        $this->addSql('CREATE INDEX IDX_EAA81A4CCB4B68F ON transactions (payee_id)');
        $this->addSql('CREATE INDEX IDX_EAA81A4CBAD26311 ON transactions (tag_id)');
        $this->addSql('CREATE INDEX type_idx_transactions ON transactions (type)');
        $this->addSql('CREATE INDEX spent_idx_transactions ON transactions (spent_at)');
        $this->addSql('CREATE INDEX account_id_spent_at_idx_transactions ON transactions (account_id, spent_at)');
        $this->addSql('CREATE INDEX account_recipient_id_spent_at_idx_transactions ON transactions (account_recipient_id, spent_at)');
        $this->addSql('CREATE INDEX category_id_account_id_spent_at_idx_transactions ON transactions (category_id, account_id, spent_at)');
        $this->addSql('CREATE INDEX tag_id_account_id_spent_at_idx_transactions ON transactions (tag_id, account_id, spent_at)');
    }

    private function downPostgresql(): void
    {
        // PostgreSQL supports ALTER COLUMN TYPE directly
        $this->addSql('ALTER TABLE budgets_elements_limits ALTER COLUMN amount TYPE NUMERIC(19, 2)');
        $this->addSql('ALTER TABLE currencies_rates ALTER COLUMN rate TYPE NUMERIC(16, 8)');
        $this->addSql('ALTER TABLE transactions ALTER COLUMN amount TYPE NUMERIC(19, 2)');
        $this->addSql('ALTER TABLE transactions ALTER COLUMN amount_recipient TYPE NUMERIC(19, 2)');
    }
}
