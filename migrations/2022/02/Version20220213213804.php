<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220213213804 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE refresh_tokens_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE billing_address (id UUID NOT NULL, user_id UUID NOT NULL, company_name VARCHAR(255) DEFAULT NULL, name VARCHAR(255) DEFAULT NULL, email VARCHAR(255) DEFAULT NULL, phone_intl_code VARCHAR(255) DEFAULT NULL, phone_area_code VARCHAR(255) DEFAULT NULL, phone_number VARCHAR(255) DEFAULT NULL, document_type VARCHAR(255) DEFAULT NULL, document_number VARCHAR(255) DEFAULT NULL, address_line1 TEXT DEFAULT NULL, address_line2 VARCHAR(255) DEFAULT NULL, address_number VARCHAR(255) DEFAULT NULL, address_district VARCHAR(255) DEFAULT NULL, address_city VARCHAR(255) DEFAULT NULL, address_state VARCHAR(255) DEFAULT NULL, address_country VARCHAR(255) DEFAULT NULL, address_zip_code VARCHAR(255) DEFAULT NULL, deleted_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE country (id UUID NOT NULL, currency_id UUID DEFAULT NULL, primary_timezone VARCHAR(255) DEFAULT NULL, timezones JSON NOT NULL, name VARCHAR(255) NOT NULL, code VARCHAR(255) NOT NULL, primary_locale VARCHAR(255) DEFAULT NULL, is_active BOOLEAN DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_5373C96638248176 ON country (currency_id)');
        $this->addSql('CREATE TABLE currency (id UUID NOT NULL, name VARCHAR(255) NOT NULL, code VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE language (id UUID NOT NULL, name VARCHAR(255) NOT NULL, code VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE refresh_tokens (id INT NOT NULL, refresh_token VARCHAR(128) NOT NULL, username VARCHAR(255) NOT NULL, valid TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_9BACE7E1C74F2195 ON refresh_tokens (refresh_token)');
        $this->addSql('CREATE TABLE user_account (id UUID NOT NULL, name VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, password VARCHAR(255) DEFAULT NULL, roles JSON NOT NULL, country VARCHAR(255) DEFAULT NULL, locale VARCHAR(255) DEFAULT NULL, timezone VARCHAR(255) DEFAULT NULL, profile_picture VARCHAR(255) DEFAULT NULL, allow_email_marketing BOOLEAN DEFAULT \'true\' NOT NULL, is_active BOOLEAN DEFAULT \'true\' NOT NULL, is_test_user BOOLEAN DEFAULT \'false\' NOT NULL, is_email_validated BOOLEAN DEFAULT \'false\' NOT NULL, email_validated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, last_login_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, is_blocked BOOLEAN DEFAULT \'false\' NOT NULL, admin_notes TEXT DEFAULT NULL, deleted_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_253B48AEE7927C74 ON user_account (email)');
        $this->addSql('CREATE INDEX IDX_253B48AEE7927C74 ON user_account (email)');
        $this->addSql('CREATE TABLE user_activity (id UUID NOT NULL, user_id UUID NOT NULL, action VARCHAR(255) NOT NULL, details JSON NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_4CF9ED5AA76ED395 ON user_activity (user_id)');
        $this->addSql('CREATE TABLE user_integration (id UUID NOT NULL, user_id UUID NOT NULL, platform VARCHAR(255) NOT NULL, external_id VARCHAR(255) DEFAULT NULL, access_token TEXT DEFAULT NULL, access_token_expires_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, details JSON NOT NULL, is_active BOOLEAN DEFAULT \'true\' NOT NULL, deleted_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_54F2A40EA76ED395 ON user_integration (user_id)');
        $this->addSql('CREATE TABLE user_password_reset_token (id UUID NOT NULL, user_id UUID NOT NULL, token VARCHAR(255) NOT NULL, expires_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_D55A77C9A76ED395 ON user_password_reset_token (user_id)');
        $this->addSql('CREATE TABLE user_review (id UUID NOT NULL, user_id UUID NOT NULL, reviewer_id UUID NOT NULL, review TEXT DEFAULT NULL, rating INT NOT NULL, deleted_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_1C119AFBA76ED395 ON user_review (user_id)');
        $this->addSql('CREATE INDEX IDX_1C119AFB70574616 ON user_review (reviewer_id)');
        $this->addSql('CREATE TABLE user_setting (id UUID NOT NULL, user_id UUID DEFAULT NULL, name VARCHAR(255) DEFAULT NULL, value TEXT DEFAULT NULL, deleted_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('ALTER TABLE country ADD CONSTRAINT FK_5373C96638248176 FOREIGN KEY (currency_id) REFERENCES currency (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE user_activity ADD CONSTRAINT FK_4CF9ED5AA76ED395 FOREIGN KEY (user_id) REFERENCES user_account (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE user_integration ADD CONSTRAINT FK_54F2A40EA76ED395 FOREIGN KEY (user_id) REFERENCES user_account (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE user_password_reset_token ADD CONSTRAINT FK_D55A77C9A76ED395 FOREIGN KEY (user_id) REFERENCES user_account (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE user_review ADD CONSTRAINT FK_1C119AFBA76ED395 FOREIGN KEY (user_id) REFERENCES user_account (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE user_review ADD CONSTRAINT FK_1C119AFB70574616 FOREIGN KEY (reviewer_id) REFERENCES user_account (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE country DROP CONSTRAINT FK_5373C96638248176');
        $this->addSql('ALTER TABLE user_activity DROP CONSTRAINT FK_4CF9ED5AA76ED395');
        $this->addSql('ALTER TABLE user_integration DROP CONSTRAINT FK_54F2A40EA76ED395');
        $this->addSql('ALTER TABLE user_password_reset_token DROP CONSTRAINT FK_D55A77C9A76ED395');
        $this->addSql('ALTER TABLE user_review DROP CONSTRAINT FK_1C119AFBA76ED395');
        $this->addSql('ALTER TABLE user_review DROP CONSTRAINT FK_1C119AFB70574616');
        $this->addSql('DROP SEQUENCE refresh_tokens_id_seq CASCADE');
        $this->addSql('DROP TABLE billing_address');
        $this->addSql('DROP TABLE country');
        $this->addSql('DROP TABLE currency');
        $this->addSql('DROP TABLE language');
        $this->addSql('DROP TABLE refresh_tokens');
        $this->addSql('DROP TABLE user_account');
        $this->addSql('DROP TABLE user_activity');
        $this->addSql('DROP TABLE user_integration');
        $this->addSql('DROP TABLE user_password_reset_token');
        $this->addSql('DROP TABLE user_review');
        $this->addSql('DROP TABLE user_setting');
    }
}
