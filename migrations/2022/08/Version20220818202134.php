<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220818202134 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(
            'UPDATE video '.
            'SET moods = ( '.
                'SELECT TO_JSON(ARRAY_AGG(DISTINCT m.mood)) '.
                'FROM moment m '.
                'JOIN video_moment vm ON ( vm.moment_id = m.id ) '.
                'WHERE vm.video_id = video.id '.
            ')'
        );
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
