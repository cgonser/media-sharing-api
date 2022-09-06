<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220906131345 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $musicFiles = [
            '7-da-manha-badarojambrass.mp3' => [ "title" => "7 da Manhã", "artist" => "Badaro Jambrass" ],
            'a-moment-another-life.mp3' => [ "title" => "A moment", "artist" => "Another Life" ],
            'beat-ed-navarro.mp3' => [ "title" => "Rocksteady", "artist" => "Badaro Jambrass" ],
            'beautiful-soul-odoghan.mp3' => [ "title" => "Candy", "artist" => "Ketsa" ],
            'candy-ketsa.mp3' => [ "title" => "Doe Normaal", "artist" => "Balkan Jingles" ],
            'come-through-instrumental-another-life.mp3' => [ "title" => "Beautiful Soul", "artist" => "Odoghan" ],
            'demain-je-change-de-vie-lohstana.mp3' => [ "title" => "Demain je change de vie", "artist" => "Lohstana" ],
            'doe-normaal-balkan-jingles.mp3' => [ "title" => "Come Through (Instrumental)", "artist" => "Another life" ],
            'fortunate-son-jonworthymusic.mp3' => [ "title" => "Fortunate son", "artist" => "Jon Worthy" ],
            'fountains-far-from-home-one-man-book.mp3' => [ "title" => "Fountains Far From Home", "artist" => "One Man Book" ],
            'late-nights-lyvo.mp3' => [ "title" => "Perhaps (Clarinet)", "artist" => "Magnus Moone" ],
            'lyin-on-the-floor-belmontclub.mp3' => [ "title" => "Malone Morning", "artist" => "Roeland Ruijsch" ],
            'malone-morning-roelandruijsch.mp3' => [ "title" => "Urban Moods", "artist" => "Hedkandi" ],
            'perhaps-clarinet-magnusmoone.mp3' => [ "title" => "Late Nights", "artist" => "Lyvo" ],
            'rocksteady-badarojambrass.mp3' => [ "title" => "Beat", "artist" => "Ed Navarro" ],
            'urban-moods-hedkandi.mp3' => [ "title" => "Lyin On The Floor", "artist" => "Belmont Club" ],
        ];

        foreach ($musicFiles as $currentDisplayName => $songData) {
            $this->addSql(
                sprintf(
                    "UPDATE music ".
                    "SET artist = '%s', title = '%s', display_name = '%s' ".
                    "WHERE display_name = '%s'",
                    $songData['artist'],
                    $songData['title'],
                    $songData['artist'] . ' - ' . $songData['title'],
                    $currentDisplayName,
                )
            );
        }
    }

    public function down(Schema $schema): void
    {
    }
}
