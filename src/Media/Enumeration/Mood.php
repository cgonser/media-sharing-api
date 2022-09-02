<?php

namespace App\Media\Enumeration;

enum Mood: string
{
    case ANGRY = 'angry';
    case EXCITED = 'excited';
    case FUNNY = 'funny';
    case HAPPY = 'happy';
    case SAD = 'sad';

    public const COLORS = [
        'angry' => 'asdasd',
        'excited' => '#FF953F',
        'funny' => '#74E8E7',
        'happy' => '#FDE056',
        'sad' => '#7D9BE3',
    ];

}
