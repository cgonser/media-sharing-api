<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220617181100 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(
            "DELETE FROM user_notification_channel WHERE channel = 'push' AND device_type IS NULL"
        );

        $this->addSql(
            'DELETE FROM user_notification_channel a USING user_notification_channel b '.
            'WHERE a.id <> b.id AND a.user_id = b.user_id AND a.channel = b.channel AND a.device_type = b.device_type'
        );

        $this->addSql(
            'CREATE UNIQUE INDEX UNIQ_9C6D78FA76ED395A2F98E475E78213 '.
            'ON user_notification_channel (user_id, channel, device_type, is_active, deleted_at)'
        );
    }

    public function down(Schema $schema): void
    {
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP INDEX UNIQ_9C6D78FA76ED395A2F98E475E78213');
    }
}
