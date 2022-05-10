<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220510155836 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE moment ALTER duration TYPE NUMERIC(5, 2)');
        $this->addSql('ALTER TABLE moment ALTER duration DROP DEFAULT');
        $this->addSql('ALTER TABLE moment ALTER duration SET NOT NULL');
        $this->addSql('ALTER TABLE video_moment ADD duration NUMERIC(5, 2)');
        $this->addSql('UPDATE video_moment SET duration = ( SELECT duration FROM moment WHERE moment.id = video_moment.moment_id )');
        $this->addSql('ALTER TABLE video_moment ALTER "duration" SET NOT NULL');
        $this->addSql('ALTER TABLE video_moment ALTER "position" SET NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE video_moment DROP duration');
        $this->addSql('ALTER TABLE video_moment ALTER position DROP NOT NULL');
        $this->addSql('ALTER TABLE moment ALTER duration TYPE INT');
        $this->addSql('ALTER TABLE moment ALTER duration DROP DEFAULT');
        $this->addSql('ALTER TABLE moment ALTER duration DROP NOT NULL');
    }
}
