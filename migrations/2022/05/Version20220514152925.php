<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220514152925 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE location ADD long NUMERIC(8, 6) DEFAULT NULL');
        $this->addSql('ALTER TABLE location ADD lat NUMERIC(8, 6) DEFAULT NULL');
        $this->addSql('UPDATE location SET long = ST_X(coordinates), lat = ST_Y(coordinates)');
        $this->addSql('CREATE INDEX idx_location_long ON location (long)');
        $this->addSql('CREATE INDEX idx_location_lat ON location (lat)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE location DROP long');
        $this->addSql('ALTER TABLE location DROP lat');
    }
}
