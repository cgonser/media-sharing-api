<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220604204110 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE user_notification_channel ADD device VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE user_notification_channel ALTER details DROP NOT NULL');
        $this->addSql(
            "UPDATE user_notification_channel ".
            "SET device = REPLACE(CAST(details->'os' AS VARCHAR), '\"', ''), ".
            "external_id = REPLACE(CAST(details->'uid' AS VARCHAR), '\"', ''), ".
            "token = REPLACE(CAST(details->'token' AS VARCHAR), '\"', '') ".
            "WHERE channel = 'push'"
        );
        $this->addSql("UPDATE user_notification_channel SET details = NULL WHERE channel = 'push'");
        $this->addSql(
            "INSERT INTO user_notification_channel (id, user_id, channel, created_at) ".
            "SELECT uuid_generate_v4(), ua.id, 'email', NOW() FROM user_account ua"
        );
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE user_notification_channel DROP device');
    }
}
