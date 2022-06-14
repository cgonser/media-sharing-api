<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220614145832 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $basePublicUrl = 'https://itinair-qa-media-item.s3.eu-central-1.amazonaws.com/';

        $musicFiles = [
            'beautiful_soul-odoghan.mp3',
            'fortunate_son-jonworthymusic.mp3',
            'late_nights-lyvo.mp3',
            'lyin_on_the_floor-belmontclub.wav',
            'perhaps_clarinet-magnusmoone.mp3',
            'the_perfect_day_60_sec-hedkandi.mp3',
            'urban_moods-hedkandi.mp3',
        ];

        foreach ($musicFiles as $musicFile) {
            $displayName = $musicFile;
            $filename = 'music/'.$musicFile;
            $publicUrl = $basePublicUrl . $filename;

            $this->addSql(
                "INSERT INTO music ".
                "(id, display_name, filename, public_url, created_at, updated_at) ".
                "VALUES ".
                "(uuid_generate_v4(), '".$displayName."', '".$filename."', '".$publicUrl."', NOW(), NOW())"
            );
        }
    }

    public function down(Schema $schema): void
    {
    }
}
