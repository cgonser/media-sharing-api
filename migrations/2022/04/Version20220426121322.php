<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220426121322 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX idx_253b48aee7927c74');
        $this->addSql('ALTER TABLE user_account ADD username VARCHAR(255) NULL');
        $this->addSql('UPDATE user_account SET username = id');
        $this->addSql('ALTER TABLE user_account ALTER COLUMN username SET NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_253B48AEF85E0677 ON user_account (username)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP INDEX UNIQ_253B48AEF85E0677');
        $this->addSql('ALTER TABLE user_account DROP username');
        $this->addSql('CREATE INDEX idx_253b48aee7927c74 ON user_account (email)');
    }
}
