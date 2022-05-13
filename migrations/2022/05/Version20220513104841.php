<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220513104841 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('DROP INDEX idx_moment_location_coordinates');
        $this->addSql('DROP INDEX idx_moment_location_google_place_id');
        $this->addSql('ALTER TABLE moment ADD location_id UUID DEFAULT NULL');

        $this->addSql(
            'INSERT INTO location (id, coordinates, google_place_id, address, created_at, updated_at) '.
            'SELECT DISTINCT uuid_generate_v4(), location_coordinates, location_google_place_id, location_address, NOW(), NOW() '.
            'FROM moment WHERE location_coordinates IS NOT NULL'
        );

        $this->addSql('ALTER TABLE moment DROP location_coordinates');
        $this->addSql('ALTER TABLE moment DROP location_google_place_id');
        $this->addSql('ALTER TABLE moment DROP location_address');
        $this->addSql('ALTER TABLE moment ALTER mood SET NOT NULL');
        $this->addSql('COMMENT ON COLUMN moment.location_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE moment ADD CONSTRAINT FK_358C88A264D218E FOREIGN KEY (location_id) REFERENCES location (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_358C88A264D218E ON moment (location_id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE moment DROP CONSTRAINT FK_358C88A264D218E');
        $this->addSql('DROP INDEX IDX_358C88A264D218E');
        $this->addSql('ALTER TABLE moment ADD location_coordinates Geometry(Point) DEFAULT NULL');
        $this->addSql('ALTER TABLE moment ADD location_google_place_id VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE moment ADD location_address VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE moment DROP location_id');
        $this->addSql('ALTER TABLE moment ALTER mood DROP NOT NULL');
        $this->addSql('COMMENT ON COLUMN moment.location_coordinates IS \'(DC2Type:point)\'');
        $this->addSql('CREATE INDEX idx_moment_location_coordinates ON moment (location_coordinates)');
        $this->addSql('CREATE INDEX idx_moment_location_google_place_id ON moment (location_google_place_id)');
    }
}
