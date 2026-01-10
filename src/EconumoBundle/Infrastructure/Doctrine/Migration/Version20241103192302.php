<?php

declare(strict_types=1);

namespace App\EconumoBundle\Infrastructure\Doctrine\Migration;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241103192302 extends AbstractMigration
{
    public function getDescription(): string
    {
        return "";
    }

    public function up(Schema $schema): void
    {
        $platform = $this->connection->getDatabasePlatform()->getName();

        if ($platform === "sqlite") {
            $this->upSqlite();
        } elseif ($platform === "postgresql") {
            $this->upPostgresql();
        } else {
            $this->skipIf(
                true,
                "Migration can only be executed safely on 'sqlite' or 'postgresql'.",
            );
        }
    }

    private function upSqlite(): void
    {
        $this->addSql(
            <<<'SQL'
            CREATE TABLE budgets
            (
                id          CHAR(36)    NOT NULL --(DC2Type:uuid)
                , currency_id CHAR(36)    NOT NULL --(DC2Type:uuid)
                , user_id     CHAR(36)    NOT NULL --(DC2Type:uuid)
                , name        VARCHAR(64) NOT NULL
                , started_at  DATETIME    NOT NULL
                , created_at  DATETIME    NOT NULL --(DC2Type:datetime_immutable)
                , updated_at  DATETIME    NOT NULL
                , PRIMARY KEY (id)
                , FOREIGN KEY (currency_id) REFERENCES currencies (id) ON DELETE SET NULL
                , FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE
            )
            SQL
            ,
        );
        $this->addSql(
            "CREATE INDEX IDX_DCAA954838248176 ON budgets (currency_id)",
        );
        $this->addSql("CREATE INDEX IDX_DCAA9548A76ED395 ON budgets (user_id)");

        $this->addSql(
            <<<'SQL'
            CREATE TABLE budgets_access
            (
                budget_id   CHAR(36)            NOT NULL --(DC2Type:uuid)
                , user_id     CHAR(36)            NOT NULL --(DC2Type:uuid)
                , role        SMALLINT            NOT NULL
                , is_accepted BOOLEAN DEFAULT '0' NOT NULL
                , created_at  DATETIME            NOT NULL --(DC2Type:datetime_immutable)
                , updated_at  DATETIME            NOT NULL
                , PRIMARY KEY (budget_id, user_id)
                , FOREIGN KEY (budget_id) REFERENCES budgets (id) ON DELETE CASCADE
                , FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE
            )
            SQL
            ,
        );
        $this->addSql(
            "CREATE INDEX IDX_9300F12F36ABA6B8 ON budgets_access (budget_id)",
        );
        $this->addSql(
            "CREATE INDEX IDX_9300F12FA76ED395 ON budgets_access (user_id)",
        );

        $this->addSql(
            <<<'SQL'
            CREATE TABLE budgets_folders
            (
                id         CHAR(36)                    NOT NULL --(DC2Type:uuid)
                , budget_id  CHAR(36)                    NOT NULL --(DC2Type:uuid)
                , name       VARCHAR(64)                 NOT NULL
                , position   SMALLINT UNSIGNED DEFAULT 0 NOT NULL
                , created_at DATETIME                    NOT NULL --(DC2Type:datetime_immutable)
                , updated_at DATETIME                    NOT NULL
                , PRIMARY KEY (id)
                , FOREIGN KEY (budget_id) REFERENCES budgets (id) ON DELETE CASCADE
            )
            SQL
            ,
        );
        $this->addSql(
            "CREATE INDEX IDX_3975126136ABA6B8 ON budgets_folders (budget_id)",
        );

        $this->addSql(
            <<<'SQL'
            CREATE TABLE budgets_elements
            (
                id          CHAR(36)                    NOT NULL --(DC2Type:uuid)
                , budget_id   CHAR(36)                    NOT NULL --(DC2Type:uuid)
                , currency_id CHAR(36)          DEFAULT NULL       --(DC2Type:uuid)
                , folder_id   CHAR(36)          DEFAULT NULL       --(DC2Type:uuid)
                , external_id CHAR(36)                    NOT NULL --(DC2Type:uuid)
                , type        SMALLINT                    NOT NULL
                , created_at  DATETIME                    NOT NULL --(DC2Type:datetime_immutable)
                , updated_at  DATETIME                    NOT NULL
                , position    SMALLINT UNSIGNED DEFAULT 0 NOT NULL
                , PRIMARY KEY (id)
                , FOREIGN KEY (budget_id) REFERENCES budgets (id) ON DELETE CASCADE
                , FOREIGN KEY (currency_id) REFERENCES currencies (id) ON DELETE SET NULL
                , FOREIGN KEY (folder_id) REFERENCES budgets_folders (id) ON DELETE SET NULL
                , UNIQUE (budget_id, external_id)
            )
            SQL
            ,
        );
        $this->addSql(
            "CREATE INDEX IDX_EE8709C336ABA6B8 ON budgets_elements (budget_id)",
        );
        $this->addSql(
            "CREATE INDEX IDX_EE8709C338248176 ON budgets_elements (currency_id)",
        );
        $this->addSql(
            "CREATE INDEX IDX_EE8709C3162CB942 ON budgets_elements (folder_id)",
        );
        $this->addSql(
            "CREATE INDEX external_id_idx_budgets_elements ON budgets_elements (external_id)",
        );
        $this->addSql(
            "CREATE UNIQUE INDEX identifier_uniq_budgets_elements ON budgets_elements (budget_id, external_id)",
        );

        $this->addSql(
            <<<'SQL'
            CREATE TABLE budgets_elements_limits
            (
                id         CHAR(36)       NOT NULL --(DC2Type:uuid)
                , element_id CHAR(36)       NOT NULL --(DC2Type:uuid)
                , period     DATETIME       NOT NULL --(DC2Type:datetime_immutable)
                , amount     NUMERIC(19, 2) NOT NULL
                , created_at DATETIME       NOT NULL --(DC2Type:datetime_immutable)
                , updated_at DATETIME       NOT NULL
                , PRIMARY KEY (id)
                , FOREIGN KEY (element_id) REFERENCES budgets_elements (id) ON DELETE CASCADE
            )
            SQL
            ,
        );
        $this->addSql(
            "CREATE INDEX IDX_406C516F1F1F2A24 ON budgets_elements_limits (element_id)",
        );
        $this->addSql(
            "CREATE INDEX period_idx_budgets_elements_limits ON budgets_elements_limits (period)",
        );
        $this->addSql(
            "CREATE INDEX element_period_idx_budgets_elements_limits ON budgets_elements_limits (element_id, period)",
        );

        $this->addSql(
            <<<'SQL'
            CREATE TABLE budgets_envelopes
            (
                id          CHAR(36)                NOT NULL --(DC2Type:uuid)
                , budget_id   CHAR(36)                NOT NULL --(DC2Type:uuid)
                , name        VARCHAR(64) DEFAULT NULL
                , icon        VARCHAR(64) DEFAULT NULL
                , is_archived BOOLEAN     DEFAULT '0' NOT NULL
                , created_at  DATETIME                NOT NULL --(DC2Type:datetime_immutable)
                , updated_at  DATETIME                NOT NULL
                , PRIMARY KEY (id)
                , FOREIGN KEY (budget_id) REFERENCES budgets (id) ON DELETE CASCADE
            );
            SQL
            ,
        );
        $this->addSql(
            "CREATE INDEX IDX_4FF0C05436ABA6B8 ON budgets_envelopes (budget_id)",
        );

        $this->addSql(
            <<<'SQL'
            CREATE TABLE budgets_envelopes_categories
            (
                budget_envelope_id CHAR(36) NOT NULL --(DC2Type:uuid)
                , category_id        CHAR(36) NOT NULL --(DC2Type:uuid)
                , PRIMARY KEY (budget_envelope_id, category_id)
                , FOREIGN KEY (budget_envelope_id) REFERENCES budgets_envelopes (id) ON DELETE CASCADE
                , FOREIGN KEY (category_id) REFERENCES categories (id) ON DELETE CASCADE
            )
            SQL
            ,
        );
        $this->addSql(
            "CREATE INDEX IDX_8F2B05CC310C8D48 ON budgets_envelopes_categories (budget_envelope_id)",
        );
        $this->addSql(
            "CREATE INDEX IDX_8F2B05CC12469DE2 ON budgets_envelopes_categories (category_id)",
        );

        $this->addSql(
            <<<'SQL'
            CREATE TABLE budgets_excluded_accounts
            (
                budget_id  CHAR(36) NOT NULL --(DC2Type:uuid)
                , account_id CHAR(36) NOT NULL --(DC2Type:uuid)
                , PRIMARY KEY (budget_id, account_id)
                , FOREIGN KEY (budget_id) REFERENCES budgets (id) ON DELETE CASCADE
                , FOREIGN KEY (account_id) REFERENCES accounts (id) ON DELETE CASCADE
            )
            SQL
            ,
        );
        $this->addSql(
            "CREATE INDEX IDX_622BD95836ABA6B8 ON budgets_excluded_accounts (budget_id)",
        );
        $this->addSql(
            "CREATE INDEX IDX_622BD9589B6B5FBA ON budgets_excluded_accounts (account_id)",
        );
    }

    private function upPostgresql(): void
    {
        $this->addSql(
            <<<'SQL'
            CREATE TABLE budgets
            (
                id          UUID    NOT NULL
                , currency_id UUID    NOT NULL
                , user_id     UUID    NOT NULL
                , name        VARCHAR(64) NOT NULL
                , started_at  TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL
                , created_at  TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL
                , updated_at  TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL
                , PRIMARY KEY (id)
                , CONSTRAINT FK_DCAA954838248176 FOREIGN KEY (currency_id) REFERENCES currencies (id) ON DELETE SET NULL NOT DEFERRABLE
                , CONSTRAINT FK_DCAA9548A76ED395 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE NOT DEFERRABLE
            )
            SQL
            ,
        );
        $this->addSql(
            "CREATE INDEX IDX_DCAA954838248176 ON budgets (currency_id)",
        );
        $this->addSql("CREATE INDEX IDX_DCAA9548A76ED395 ON budgets (user_id)");

        $this->addSql(
            <<<'SQL'
            CREATE TABLE budgets_access
            (
                budget_id   UUID            NOT NULL
                , user_id     UUID            NOT NULL
                , role        SMALLINT            NOT NULL
                , is_accepted BOOLEAN DEFAULT '0' NOT NULL
                , created_at  TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL
                , updated_at  TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL
                , PRIMARY KEY (budget_id, user_id)
                , CONSTRAINT FK_9300F12F36ABA6B8 FOREIGN KEY (budget_id) REFERENCES budgets (id) ON DELETE CASCADE NOT DEFERRABLE
                , CONSTRAINT FK_9300F12FA76ED395 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE NOT DEFERRABLE
            )
            SQL
            ,
        );
        $this->addSql(
            "CREATE INDEX IDX_9300F12F36ABA6B8 ON budgets_access (budget_id)",
        );
        $this->addSql(
            "CREATE INDEX IDX_9300F12FA76ED395 ON budgets_access (user_id)",
        );

        $this->addSql(
            <<<'SQL'
            CREATE TABLE budgets_folders
            (
                id         UUID                    NOT NULL
                , budget_id  UUID                    NOT NULL
                , name       VARCHAR(64)                 NOT NULL
                , position   SMALLINT DEFAULT 0 NOT NULL CHECK (position >= 0)
                , created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL
                , updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL
                , PRIMARY KEY (id)
                , CONSTRAINT FK_3975126136ABA6B8 FOREIGN KEY (budget_id) REFERENCES budgets (id) ON DELETE CASCADE NOT DEFERRABLE
            )
            SQL
            ,
        );
        $this->addSql(
            "CREATE INDEX IDX_3975126136ABA6B8 ON budgets_folders (budget_id)",
        );

        $this->addSql(
            <<<'SQL'
            CREATE TABLE budgets_elements
            (
                id          UUID                    NOT NULL
                , budget_id   UUID                    NOT NULL
                , currency_id UUID          DEFAULT NULL
                , folder_id   UUID          DEFAULT NULL
                , external_id UUID                    NOT NULL
                , type        SMALLINT                    NOT NULL
                , created_at  TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL
                , updated_at  TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL
                , position    SMALLINT DEFAULT 0 NOT NULL
                , PRIMARY KEY (id)
                , CONSTRAINT FK_EE8709C336ABA6B8 FOREIGN KEY (budget_id) REFERENCES budgets (id) ON DELETE CASCADE NOT DEFERRABLE
                , CONSTRAINT FK_EE8709C338248176 FOREIGN KEY (currency_id) REFERENCES currencies (id) ON DELETE SET NULL NOT DEFERRABLE
                , CONSTRAINT FK_EE8709C3162CB942 FOREIGN KEY (folder_id) REFERENCES budgets_folders (id) ON DELETE SET NULL NOT DEFERRABLE
                , UNIQUE (budget_id, external_id)
            )
            SQL
            ,
        );
        $this->addSql(
            "CREATE INDEX IDX_EE8709C336ABA6B8 ON budgets_elements (budget_id)",
        );
        $this->addSql(
            "CREATE INDEX IDX_EE8709C338248176 ON budgets_elements (currency_id)",
        );
        $this->addSql(
            "CREATE INDEX IDX_EE8709C3162CB942 ON budgets_elements (folder_id)",
        );
        $this->addSql(
            "CREATE INDEX external_id_idx_budgets_elements ON budgets_elements (external_id)",
        );
        $this->addSql(
            "CREATE UNIQUE INDEX identifier_uniq_budgets_elements ON budgets_elements (budget_id, external_id)",
        );

        $this->addSql(
            <<<'SQL'
            CREATE TABLE budgets_elements_limits
            (
                id         UUID       NOT NULL
                , element_id UUID       NOT NULL
                , period     TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL
                , amount     NUMERIC(19, 2) NOT NULL
                , created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL
                , updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL
                , PRIMARY KEY (id)
                , CONSTRAINT FK_406C516F1F1F2A24 FOREIGN KEY (element_id) REFERENCES budgets_elements (id) ON DELETE CASCADE NOT DEFERRABLE
            )
            SQL
            ,
        );
        $this->addSql(
            "CREATE INDEX IDX_406C516F1F1F2A24 ON budgets_elements_limits (element_id)",
        );
        $this->addSql(
            "CREATE INDEX period_idx_budgets_elements_limits ON budgets_elements_limits (period)",
        );
        $this->addSql(
            "CREATE INDEX element_period_idx_budgets_elements_limits ON budgets_elements_limits (element_id, period)",
        );

        $this->addSql(
            <<<'SQL'
            CREATE TABLE budgets_envelopes
            (
                id          UUID                NOT NULL
                , budget_id   UUID                NOT NULL
                , name        VARCHAR(64) DEFAULT NULL
                , icon        VARCHAR(64) DEFAULT NULL
                , is_archived BOOLEAN     DEFAULT false NOT NULL
                , created_at  TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL
                , updated_at  TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL
                , PRIMARY KEY (id)
                , CONSTRAINT FK_4FF0C05436ABA6B8 FOREIGN KEY (budget_id) REFERENCES budgets (id) ON DELETE CASCADE NOT DEFERRABLE
            )
            SQL
            ,
        );
        $this->addSql(
            "CREATE INDEX IDX_4FF0C05436ABA6B8 ON budgets_envelopes (budget_id)",
        );

        $this->addSql(
            <<<'SQL'
            CREATE TABLE budgets_envelopes_categories
            (
                budget_envelope_id UUID NOT NULL
                , category_id        UUID NOT NULL
                , PRIMARY KEY (budget_envelope_id, category_id)
                , CONSTRAINT FK_8F2B05CC310C8D48 FOREIGN KEY (budget_envelope_id) REFERENCES budgets_envelopes (id) ON DELETE CASCADE NOT DEFERRABLE
                , CONSTRAINT FK_8F2B05CC12469DE2 FOREIGN KEY (category_id) REFERENCES categories (id) ON DELETE CASCADE NOT DEFERRABLE
            )
            SQL
            ,
        );
        $this->addSql(
            "CREATE INDEX IDX_8F2B05CC310C8D48 ON budgets_envelopes_categories (budget_envelope_id)",
        );
        $this->addSql(
            "CREATE INDEX IDX_8F2B05CC12469DE2 ON budgets_envelopes_categories (category_id)",
        );

        $this->addSql(
            <<<'SQL'
            CREATE TABLE budgets_excluded_accounts
            (
                budget_id  UUID NOT NULL
                , account_id UUID NOT NULL
                , PRIMARY KEY (budget_id, account_id)
                , CONSTRAINT FK_622BD95836ABA6B8 FOREIGN KEY (budget_id) REFERENCES budgets (id) ON DELETE CASCADE NOT DEFERRABLE
                , CONSTRAINT FK_622BD9589B6B5FBA FOREIGN KEY (account_id) REFERENCES accounts (id) ON DELETE CASCADE NOT DEFERRABLE
            )
            SQL
            ,
        );
        $this->addSql(
            "CREATE INDEX IDX_622BD95836ABA6B8 ON budgets_excluded_accounts (budget_id)",
        );
        $this->addSql(
            "CREATE INDEX IDX_622BD9589B6B5FBA ON budgets_excluded_accounts (account_id)",
        );
    }

    public function down(Schema $schema): void
    {
        $platform = $this->connection->getDatabasePlatform()->getName();

        if ($platform === "sqlite") {
            $this->downSqlite();
        } elseif ($platform === "postgresql") {
            $this->downPostgresql();
        } else {
            $this->skipIf(
                true,
                "Migration can only be executed safely on 'sqlite' or 'postgresql'.",
            );
        }
    }

    private function downSqlite(): void
    {
        $this->addSql('DROP TABLE IF EXISTS "budgets_elements_limits"');
        $this->addSql('DROP TABLE IF EXISTS "budgets_elements"');
        $this->addSql('DROP TABLE IF EXISTS "budgets_envelopes_categories"');
        $this->addSql('DROP TABLE IF EXISTS "budgets_envelopes"');
        $this->addSql('DROP TABLE IF EXISTS "budgets_access"');
        $this->addSql('DROP TABLE IF EXISTS "budgets_folders"');
        $this->addSql('DROP TABLE IF EXISTS "budgets_excluded_accounts"');
        $this->addSql('DROP TABLE IF EXISTS "budgets"');
    }

    private function downPostgresql(): void
    {
        $this->addSql("DROP TABLE IF EXISTS budgets_elements_limits");
        $this->addSql("DROP TABLE IF EXISTS budgets_elements");
        $this->addSql("DROP TABLE IF EXISTS budgets_envelopes_categories");
        $this->addSql("DROP TABLE IF EXISTS budgets_envelopes");
        $this->addSql("DROP TABLE IF EXISTS budgets_access");
        $this->addSql("DROP TABLE IF EXISTS budgets_folders");
        $this->addSql("DROP TABLE IF EXISTS budgets_excluded_accounts");
        $this->addSql("DROP TABLE IF EXISTS budgets");
    }
}
