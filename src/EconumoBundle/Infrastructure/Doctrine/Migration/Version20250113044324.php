<?php

declare(strict_types=1);

namespace App\EconumoBundle\Infrastructure\Doctrine\Migration;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250113044324 extends AbstractMigration
{
    private ?ContainerInterface $container = null;

    public function getDescription() : string
    {
        return 'Add additional columns to currencies table';
    }

    public function up(Schema $schema) : void
    {
        $platform = $this->connection->getDatabasePlatform()->getName();

        if ($platform === 'sqlite') {
            $this->addSql("ALTER TABLE currencies ADD COLUMN name VARCHAR(36) DEFAULT NULL");
            $this->addSql("ALTER TABLE currencies ADD COLUMN fraction_digits SMALLINT DEFAULT '2' NOT NULL");
        } elseif ($platform === 'postgresql') {
            $this->addSql("ALTER TABLE currencies ADD COLUMN name VARCHAR(36) DEFAULT NULL");
            $this->addSql("ALTER TABLE currencies ADD COLUMN fraction_digits SMALLINT DEFAULT 2 NOT NULL");
        }

        $this->warnIf(true, 'Please update your currencies fraction digits manually, by calling a command: bin/console app:restore-currency-fraction-digits');
    }

    public function down(Schema $schema) : void
    {
        $platform = $this->connection->getDatabasePlatform()->getName();

        if ($platform === 'sqlite' || $platform === 'postgresql') {
            $this->addSql('ALTER TABLE currencies DROP COLUMN name');
            $this->addSql('ALTER TABLE currencies DROP COLUMN fraction_digits');
        }
    }
}
