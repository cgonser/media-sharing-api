<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220515144548 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE user_notification_channel (id UUID NOT NULL, user_id UUID NOT NULL, channel VARCHAR(255) NOT NULL, external_id VARCHAR(255) DEFAULT NULL, token TEXT DEFAULT NULL, expires_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, details JSON NOT NULL, is_active BOOLEAN DEFAULT true NOT NULL, deleted_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_9C6D78FA76ED395 ON user_notification_channel (user_id)');
        $this->addSql('COMMENT ON COLUMN user_notification_channel.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN user_notification_channel.user_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE user_notification_channel ADD CONSTRAINT FK_9C6D78FA76ED395 FOREIGN KEY (user_id) REFERENCES user_account (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE user_notification_channel');
    }
}
