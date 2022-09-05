<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220905175630 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("UPDATE music SET is_active = FALSE");

        $basePublicUrl = 'https://cdn.itinair.com/music/';
        $basePath = 's3://cdn.itinair.com/music/';

        $musicFiles = [
            '7-da-manha-badarojambrass.mp3',
            'a-moment-another-life.mp3',
            'beat-ed-navarro.mp3',
            'beautiful-soul-odoghan.mp3',
            'candy-ketsa.mp3',
            'come-through-instrumental-another-life.mp3',
            'demain-je-change-de-vie-lohstana.mp3',
            'doe-normaal-balkan-jingles.mp3',
            'fortunate-son-jonworthymusic.mp3',
            'fountains-far-from-home-one-man-book.mp3',
            'late-nights-lyvo.mp3',
            'lyin-on-the-floor-belmontclub.mp3',
            'malone-morning-roelandruijsch.mp3',
            'perhaps-clarinet-magnusmoone.mp3',
            'rocksteady-badarojambrass.mp3',
            'urban-moods-hedkandi.mp3',
        ];

        foreach ($musicFiles as $filename) {
            $path = $basePath . $filename;
            $publicUrl = $basePublicUrl . $filename;

            $this->addSql(
                "INSERT INTO music ".
                "(id, display_name, filename, public_url, created_at, updated_at) ".
                "VALUES ".
                "(uuid_generate_v4(), '".$filename."', '".$path."', '".$publicUrl."', NOW(), NOW())"
            );
        }
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
