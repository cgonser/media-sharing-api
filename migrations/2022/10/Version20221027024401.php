<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221027024401 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql("UPDATE music SET is_active = FALSE");

        $basePublicUrl = 'https://cdn.itinair.com/music/';
        $basePath = 's3://cdn.itinair.com/music/';

        $musicFiles = [
            '7-da-manha-badarojambrass.mp3' => [ "title" => "7 da Manhã", "artist" => "Badaro Jambrass" ],
            'a-little-better-alex-figueira.mp3' => [ "title" => "A Little Better", "artist" => "Alex Figueira" ],
            'a-moment-another-day.mp3' => [ "title" => "A moment", "artist" => "Another Day" ],
            'heading-for-bamako-dieter-van-der-westen.mp3' => [ "title" => "Heading for Bamako", "artist" => "Dieter van der Westen" ],
            'paris-gipsy-swing-dieter-van-der-westen.mp3' => [ "title" => "Paris Gipsy Swing", "artist" => "Dieter van der Westen" ],
        ];

        foreach ($musicFiles as $filename => $songData) {
            $path = $basePath . $filename;
            $publicUrl = $basePublicUrl . $filename;

            $this->addSql(
                "INSERT INTO music ".
                "(id, display_name, filename, artist, title, public_url, created_at, updated_at) ".
                "VALUES ".
                "(uuid_generate_v4(), '".$filename."', '".$path."', '".$songData['artist']."', '".$songData['title']."', '".$publicUrl."', NOW(), NOW())"
            );
        }
    }

    public function down(Schema $schema): void
    {
    }
}
