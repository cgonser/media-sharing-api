<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220502165546 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('UPDATE moment SET recorded_at = CURRENT_DATE WHERE recorded_at IS NULL');
        $this->addSql('ALTER TABLE moment ALTER recorded_at SET NOT NULL');

        $this->addSql('ALTER TABLE moment ADD recorded_on DATE DEFAULT NULL');
        $this->addSql('UPDATE moment SET recorded_on = recorded_at::DATE');
        $this->addSql('ALTER TABLE moment ALTER recorded_on SET NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE moment DROP recorded_on');
        $this->addSql('ALTER TABLE moment ALTER recorded_at DROP NOT NULL');
    }
}
