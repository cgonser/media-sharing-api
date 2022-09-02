<?php

namespace App\Media\Service;

use Intervention\Image\Gd\Font;
use Intervention\Image\ImageManagerStatic as Image;

class VideoExportAssetManager
{
    public function __construct(
        private readonly string $fontPath,
    ) {
    }

    public function createTextualImage(string $text, string|array $fontColor = null): string
    {
        $img = Image::canvas(720, 48);

        if (null === $fontColor || 'transparent' === $fontColor) {
            $fontColor = [255, 255, 255, 0.5];
        }

        $img->text($text, 360, 40, function (Font $font) use ($fontColor) {
            $font->size(48);
            $font->file($this->fontPath.'Roboto-Medium.ttf');
            $font->align('center');
            $font->color('#000000');
//            $font->color($fontColor);
        });

        $img->encode('png');

        return $img->getEncoded();
    }
}
