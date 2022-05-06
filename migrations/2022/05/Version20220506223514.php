<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220506223514 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE moment DROP location');

        $this->addSql('ALTER TABLE moment ADD location_coordinates Geometry(Point) DEFAULT NULL');
        $this->addSql("COMMENT ON COLUMN moment.location_coordinates IS '(DC2Type:point)'");

        $this->addSql('ALTER TABLE moment ADD location_google_place_id VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE moment ADD location_address VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE moment DROP location_google_place_id');
        $this->addSql('ALTER TABLE moment DROP location_address');
        $this->addSql('ALTER TABLE moment DROP location_coordinates');
    }
}
