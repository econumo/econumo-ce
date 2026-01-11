<?php

declare(strict_types=1);

namespace App\EconumoBundle\Infrastructure\Doctrine\Migration;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Ramsey\Uuid\Uuid;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241222201140 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $platform = $this->connection->getDatabasePlatform()->getName();

        if ($platform === 'sqlite' || $platform === 'postgresql') {
            $users = $this->connection->fetchAllAssociative('SELECT id, created_at, updated_at FROM users;');
            foreach ($users as $i => $user) {
                $id = (string) Uuid::uuid4();
                $userId = $user['id'];
                $createdAt = $user['created_at'];
                $updatedAt = $user['updated_at'];
                $this->addSql(sprintf("INSERT INTO users_options (id, user_id, name, value, created_at, updated_at) VALUES ('%s', '%s', 'onboarding', 'started', '%s', '%s')", $id, $userId, $createdAt, $updatedAt));
            }
        }
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $platform = $this->connection->getDatabasePlatform()->getName();

        if ($platform === 'sqlite' || $platform === 'postgresql') {
            $this->addSql("DELETE FROM users_options WHERE name = 'onboarding';");
        }
    }
}
