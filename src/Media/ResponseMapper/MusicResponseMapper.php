<?php

namespace App\Media\ResponseMapper;

use App\Media\Dto\MusicDto;
use App\Media\Entity\Music;

class MusicResponseMapper
{
    public function map(Music $music): MusicDto
    {
        $musicDto = new MusicDto();
        $musicDto->id = $music->getId()->toString();
        $musicDto->displayName = $music->getDisplayName();
        $musicDto->artist = $music->getArtist();
        $musicDto->title = $music->getTitle();
        $musicDto->publicUrl = $music->getPublicUrl();
        $musicDto->duration = $music->getDuration();

        return $musicDto;
    }

    public function mapMultiple(array $musics): array
    {
        return array_map(fn ($music) => $this->map($music), $musics);
    }
}
