<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220507151927 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE moment ADD status VARCHAR(255) DEFAULT NULL');
        $this->addSql(
            "UPDATE moment SET status = 'published' ".
            "WHERE EXISTS ( ".
            "   SELECT 1 ".
            "   FROM moment_media_item vmi ".
            "   JOIN media_item mi ON ( mi.id = vmi.media_item_id ) ".
            "   WHERE vmi.moment_id = moment.id ".
            "   AND mi.status = 'available' ".
            ")");
        $this->addSql("UPDATE moment SET status = 'pending' WHERE status IS NULL");
        $this->addSql('ALTER TABLE moment ALTER COLUMN status SET NOT NULL');

        $this->addSql('ALTER TABLE moment ADD published_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE moment DROP status');
        $this->addSql('ALTER TABLE moment DROP published_at');
    }
}
