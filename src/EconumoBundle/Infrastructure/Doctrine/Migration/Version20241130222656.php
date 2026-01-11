<?php

declare(strict_types=1);

namespace App\EconumoBundle\Infrastructure\Doctrine\Migration;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241130222656 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $platform = $this->connection->getDatabasePlatform()->getName();

        if ($platform === 'sqlite') {
            $this->addSql("ALTER TABLE users ADD COLUMN is_active BOOLEAN DEFAULT '1' NOT NULL");
        } elseif ($platform === 'postgresql') {
            $this->addSql("ALTER TABLE users ADD COLUMN is_active BOOLEAN DEFAULT true NOT NULL");
        }
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $platform = $this->connection->getDatabasePlatform()->getName();

        if ($platform === 'sqlite') {
            $this->addSql('ALTER TABLE users DROP COLUMN is_active');
        } elseif ($platform === 'postgresql') {
            $this->addSql('ALTER TABLE users DROP COLUMN is_active');
        }
    }
}
