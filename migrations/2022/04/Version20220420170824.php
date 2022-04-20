<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220420170824 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE media_item (id UUID NOT NULL, status VARCHAR(255) NOT NULL, type VARCHAR(255) NOT NULL, public_url TEXT DEFAULT NULL, filename VARCHAR(255) DEFAULT NULL, extension VARCHAR(255) DEFAULT NULL, mime_type VARCHAR(255) DEFAULT NULL, comments TEXT DEFAULT NULL, upload_url TEXT DEFAULT NULL, upload_url_valid_until TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, deleted_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN media_item.id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE moment_media_item (id UUID NOT NULL, moment_id UUID NOT NULL, media_item_id UUID NOT NULL, deleted_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_AE98EE8BABE99143 ON moment_media_item (moment_id)');
        $this->addSql('CREATE INDEX IDX_AE98EE8B73B8D417 ON moment_media_item (media_item_id)');
        $this->addSql('COMMENT ON COLUMN moment_media_item.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN moment_media_item.moment_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN moment_media_item.media_item_id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE video_media_item (id UUID NOT NULL, video_id UUID NOT NULL, media_item_id UUID NOT NULL, deleted_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_5CFBE27B29C1004E ON video_media_item (video_id)');
        $this->addSql('CREATE INDEX IDX_5CFBE27B73B8D417 ON video_media_item (media_item_id)');
        $this->addSql('COMMENT ON COLUMN video_media_item.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN video_media_item.video_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN video_media_item.media_item_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE moment_media_item ADD CONSTRAINT FK_AE98EE8BABE99143 FOREIGN KEY (moment_id) REFERENCES moment (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE moment_media_item ADD CONSTRAINT FK_AE98EE8B73B8D417 FOREIGN KEY (media_item_id) REFERENCES media_item (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE video_media_item ADD CONSTRAINT FK_5CFBE27B29C1004E FOREIGN KEY (video_id) REFERENCES video (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE video_media_item ADD CONSTRAINT FK_5CFBE27B73B8D417 FOREIGN KEY (media_item_id) REFERENCES media_item (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE moment_media_item DROP CONSTRAINT FK_AE98EE8B73B8D417');
        $this->addSql('ALTER TABLE video_media_item DROP CONSTRAINT FK_5CFBE27B73B8D417');
        $this->addSql('DROP TABLE media_item');
        $this->addSql('DROP TABLE moment_media_item');
        $this->addSql('DROP TABLE video_media_item');
    }
}
