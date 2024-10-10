<?php

namespace Webwizardsusa\Larafeed\ITunes\Categories;

enum Music: string
{
    case MUSIC_COMMENTARY = "Music Commentary";
    case MUSIC_HISTORY = "Music History";
    case MUSIC_INTERVIEWS = "Music Interviews";

    public static function name(): string
    {
        return 'Music';
    }
}
