<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220415143030 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE moment (id UUID NOT NULL, user_id UUID NOT NULL, mood VARCHAR(255) DEFAULT NULL, location VARCHAR(255) DEFAULT NULL, duration INT DEFAULT NULL, recorded_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_358C88A2A76ED395 ON moment (user_id)');
        $this->addSql('COMMENT ON COLUMN moment.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN moment.user_id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE video (id UUID NOT NULL, user_id UUID NOT NULL, description TEXT DEFAULT NULL, mood VARCHAR(255) DEFAULT NULL, thumbnail VARCHAR(255) DEFAULT NULL, locations JSON DEFAULT NULL, duration INT DEFAULT NULL, likes INT DEFAULT NULL, comments INT DEFAULT NULL, recorded_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_7CC7DA2CA76ED395 ON video (user_id)');
        $this->addSql('COMMENT ON COLUMN video.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN video.user_id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE video_moment (id UUID NOT NULL, video_id UUID NOT NULL, moment_id UUID NOT NULL, position INT DEFAULT NULL, deleted_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_DDB286F29C1004E ON video_moment (video_id)');
        $this->addSql('CREATE INDEX IDX_DDB286FABE99143 ON video_moment (moment_id)');
        $this->addSql('COMMENT ON COLUMN video_moment.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN video_moment.video_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN video_moment.moment_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE moment ADD CONSTRAINT FK_358C88A2A76ED395 FOREIGN KEY (user_id) REFERENCES user_account (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE video ADD CONSTRAINT FK_7CC7DA2CA76ED395 FOREIGN KEY (user_id) REFERENCES user_account (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE video_moment ADD CONSTRAINT FK_DDB286F29C1004E FOREIGN KEY (video_id) REFERENCES video (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE video_moment ADD CONSTRAINT FK_DDB286FABE99143 FOREIGN KEY (moment_id) REFERENCES moment (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE video_moment DROP CONSTRAINT FK_DDB286FABE99143');
        $this->addSql('ALTER TABLE video_moment DROP CONSTRAINT FK_DDB286F29C1004E');
        $this->addSql('DROP TABLE moment');
        $this->addSql('DROP TABLE video');
        $this->addSql('DROP TABLE video_moment');
    }
}
