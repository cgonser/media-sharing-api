<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220505162654 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE user_account ADD video_count INT DEFAULT 0 NOT NULL');
        $this->addSql('ALTER TABLE video ADD status VARCHAR(255) DEFAULT NULL');
        $this->addSql(
            "UPDATE video SET status = 'published' ".
            "WHERE EXISTS ( ".
            "   SELECT 1 ".
            "   FROM video_media_item vmi ".
            "   JOIN media_item mi ON ( mi.id = vmi.media_item_id ) ".
            "   WHERE vmi.video_id = video.id ".
            "   AND mi.status = 'available' ".
            ")");
        $this->addSql("UPDATE video SET status = 'pending' WHERE status IS NULL");
        $this->addSql('ALTER TABLE video ALTER COLUMN status SET NOT NULL');
        $this->addSql('ALTER TABLE video ADD published_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE user_account DROP video_count');
        $this->addSql('ALTER TABLE video DROP status');
        $this->addSql('ALTER TABLE video DROP published_at');
    }
}
