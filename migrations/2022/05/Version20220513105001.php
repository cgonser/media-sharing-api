<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220513105001 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE video_location (id UUID NOT NULL, video_id UUID NOT NULL, location_id UUID NOT NULL, deleted_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_F61CBB7729C1004E ON video_location (video_id)');
        $this->addSql('CREATE INDEX IDX_F61CBB7764D218E ON video_location (location_id)');
        $this->addSql('COMMENT ON COLUMN video_location.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN video_location.video_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN video_location.location_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE video_location ADD CONSTRAINT FK_F61CBB7729C1004E FOREIGN KEY (video_id) REFERENCES video (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE video_location ADD CONSTRAINT FK_F61CBB7764D218E FOREIGN KEY (location_id) REFERENCES location (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE video DROP locations');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE video_location');
        $this->addSql('ALTER TABLE video ADD locations jsonb DEFAULT NULL');
        $this->addSql('ALTER TABLE media_item ALTER extension DROP NOT NULL');
    }
}
