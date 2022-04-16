<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220416163704 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE video_comment (id UUID NOT NULL, video_id UUID NOT NULL, user_id UUID NOT NULL, comment TEXT DEFAULT NULL, deleted_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_7199BBC129C1004E ON video_comment (video_id)');
        $this->addSql('CREATE INDEX IDX_7199BBC1A76ED395 ON video_comment (user_id)');
        $this->addSql('COMMENT ON COLUMN video_comment.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN video_comment.video_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN video_comment.user_id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE video_like (id UUID NOT NULL, video_id UUID NOT NULL, user_id UUID NOT NULL, deleted_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_ABF41D6F29C1004E ON video_like (video_id)');
        $this->addSql('CREATE INDEX IDX_ABF41D6FA76ED395 ON video_like (user_id)');
        $this->addSql('COMMENT ON COLUMN video_like.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN video_like.video_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN video_like.user_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE video_comment ADD CONSTRAINT FK_7199BBC129C1004E FOREIGN KEY (video_id) REFERENCES video (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE video_comment ADD CONSTRAINT FK_7199BBC1A76ED395 FOREIGN KEY (user_id) REFERENCES user_account (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE video_like ADD CONSTRAINT FK_ABF41D6F29C1004E FOREIGN KEY (video_id) REFERENCES video (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE video_like ADD CONSTRAINT FK_ABF41D6FA76ED395 FOREIGN KEY (user_id) REFERENCES user_account (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE user_account ADD phone_number VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP TABLE video_comment');
        $this->addSql('DROP TABLE video_like');
        $this->addSql('ALTER TABLE user_account DROP phone_number');
    }
}
