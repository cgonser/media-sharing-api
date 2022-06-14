<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220614145823 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE music (id UUID NOT NULL, display_name VARCHAR(255) NOT NULL, filename TEXT DEFAULT NULL, public_url TEXT DEFAULT NULL, duration NUMERIC(6, 2) DEFAULT NULL, is_active BOOLEAN DEFAULT true NOT NULL, deleted_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN music.id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE video ADD music_id UUID DEFAULT NULL');
        $this->addSql('ALTER TABLE video ADD override_moments_audio BOOLEAN DEFAULT true NOT NULL');
        $this->addSql('COMMENT ON COLUMN video.music_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE video ADD CONSTRAINT FK_7CC7DA2C399BBB13 FOREIGN KEY (music_id) REFERENCES music (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_7CC7DA2C399BBB13 ON video (music_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE video DROP CONSTRAINT FK_7CC7DA2C399BBB13');
        $this->addSql('DROP TABLE music');
        $this->addSql('DROP INDEX IDX_7CC7DA2C399BBB13');
        $this->addSql('ALTER TABLE video DROP music_id');
        $this->addSql('ALTER TABLE video DROP override_moments_audio');
    }
}
