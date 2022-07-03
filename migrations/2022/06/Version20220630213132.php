<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220630213132 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE moment ALTER duration TYPE INT');
        $this->addSql('ALTER TABLE moment ALTER duration DROP DEFAULT');
        $this->addSql('ALTER TABLE moment ALTER duration DROP NOT NULL');
        $this->addSql('CREATE INDEX IDX_9C6D78FA76ED395A2F98E475E78213 ON user_notification_channel (user_id, channel, device_type)');
        $this->addSql('ALTER INDEX uniq_9c6d78fa76ed395a2f98e475e78213 RENAME TO UNIQ_9C6D78FA76ED395A2F98E475E782131B5771DD4AF38FD1');
        $this->addSql('ALTER TABLE video_moment ALTER duration TYPE INT');
        $this->addSql('ALTER TABLE video_moment ALTER duration DROP DEFAULT');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP INDEX IDX_9C6D78FA76ED395A2F98E475E78213');
        $this->addSql('ALTER INDEX uniq_9c6d78fa76ed395a2f98e475e782131b5771dd4af38fd1 RENAME TO uniq_9c6d78fa76ed395a2f98e475e78213');
        $this->addSql('ALTER TABLE video_moment ALTER duration TYPE NUMERIC(5, 2)');
        $this->addSql('ALTER TABLE video_moment ALTER duration DROP DEFAULT');
        $this->addSql('ALTER TABLE moment ALTER duration TYPE NUMERIC(5, 2)');
        $this->addSql('ALTER TABLE moment ALTER duration DROP DEFAULT');
        $this->addSql('ALTER TABLE moment ALTER duration SET NOT NULL');
    }
}
