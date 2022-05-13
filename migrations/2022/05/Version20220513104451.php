<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220513104451 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE location (id UUID NOT NULL, coordinates Geometry(Point) DEFAULT NULL, google_place_id VARCHAR(255) DEFAULT NULL, address VARCHAR(255) DEFAULT NULL, deleted_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX idx_location_coordinates ON location (coordinates)');
        $this->addSql('CREATE INDEX idx_location_google_place_id ON location (google_place_id)');
        $this->addSql('COMMENT ON COLUMN location.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN location.coordinates IS \'(DC2Type:point)\'');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE location');
    }
}
