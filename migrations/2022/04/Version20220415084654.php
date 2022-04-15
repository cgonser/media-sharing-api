<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220415084654 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP SEQUENCE refresh_tokens_id_seq CASCADE');
        $this->addSql('CREATE TABLE user_follow (id UUID NOT NULL, follower_id UUID NOT NULL, following_id UUID NOT NULL, is_approved BOOLEAN DEFAULT NULL, deleted_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_D665F4DAC24F853 ON user_follow (follower_id)');
        $this->addSql('CREATE INDEX IDX_D665F4D1816E3A3 ON user_follow (following_id)');
        $this->addSql('COMMENT ON COLUMN user_follow.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN user_follow.follower_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN user_follow.following_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE user_follow ADD CONSTRAINT FK_D665F4DAC24F853 FOREIGN KEY (follower_id) REFERENCES user_account (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE user_follow ADD CONSTRAINT FK_D665F4D1816E3A3 FOREIGN KEY (following_id) REFERENCES user_account (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('DROP TABLE refresh_tokens');
        $this->addSql('DROP TABLE user_review');
        $this->addSql('ALTER TABLE user_account ADD display_name VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE user_account ADD bio TEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE user_account ADD is_profile_private BOOLEAN DEFAULT false NOT NULL');
        $this->addSql('ALTER TABLE user_account ALTER profile_picture TYPE TEXT');
        $this->addSql('ALTER TABLE user_account ALTER profile_picture DROP DEFAULT');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('CREATE SEQUENCE refresh_tokens_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE refresh_tokens (id INT NOT NULL, refresh_token VARCHAR(128) NOT NULL, username VARCHAR(255) NOT NULL, valid TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX uniq_9bace7e1c74f2195 ON refresh_tokens (refresh_token)');
        $this->addSql('CREATE TABLE user_review (id UUID NOT NULL, user_id UUID NOT NULL, reviewer_id UUID NOT NULL, review TEXT DEFAULT NULL, rating INT NOT NULL, deleted_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX idx_1c119afb70574616 ON user_review (reviewer_id)');
        $this->addSql('CREATE INDEX idx_1c119afba76ed395 ON user_review (user_id)');
        $this->addSql('COMMENT ON COLUMN user_review.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN user_review.user_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN user_review.reviewer_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE user_review ADD CONSTRAINT fk_1c119afba76ed395 FOREIGN KEY (user_id) REFERENCES user_account (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE user_review ADD CONSTRAINT fk_1c119afb70574616 FOREIGN KEY (reviewer_id) REFERENCES user_account (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('DROP TABLE user_follow');
        $this->addSql('ALTER TABLE user_account DROP display_name');
        $this->addSql('ALTER TABLE user_account DROP bio');
        $this->addSql('ALTER TABLE user_account DROP is_profile_private');
        $this->addSql('ALTER TABLE user_account ALTER profile_picture TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE user_account ALTER profile_picture DROP DEFAULT');
    }
}
