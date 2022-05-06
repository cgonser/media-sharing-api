<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220506170203 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE moment DROP location');
        $this->addSql('ALTER TABLE moment ADD location POINT');
        $this->addSql('COMMENT ON COLUMN moment.location IS \'(DC2Type:point)\'');
    }

    public function down(Schema $schema): void
    {
    }
}
