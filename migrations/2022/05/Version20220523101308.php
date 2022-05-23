<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220523101308 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE location ALTER long TYPE NUMERIC(9, 6)');
        $this->addSql('ALTER TABLE location ALTER lat TYPE NUMERIC(9, 6)');
        $this->addSql('ALTER TABLE media_item ALTER extension SET NOT NULL');
        $this->addSql('ALTER TABLE moment ALTER location_id SET NOT NULL');
        $this->addSql('ALTER TABLE user_account ALTER name DROP NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE media_item ALTER extension DROP NOT NULL');
        $this->addSql('ALTER TABLE user_account ALTER name SET NOT NULL');
        $this->addSql('ALTER TABLE location ALTER long TYPE NUMERIC(8, 6)');
        $this->addSql('ALTER TABLE location ALTER lat TYPE NUMERIC(8, 6)');
        $this->addSql('ALTER TABLE moment ALTER location_id DROP NOT NULL');
    }
}
