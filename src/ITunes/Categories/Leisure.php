<?php

namespace Webwizardsusa\Larafeed\ITunes\Categories;

enum Leisure: string
{
    case ANIMATION_AND_MANGA = "Animation & Manga";
    case AUTOMOTIVE = "Automotive";
    case AVIATION = "Aviation";
    case CRAFTS = "Crafts";
    case GAMES = "Games";
    case HOBBIES = "Hobbies";
    case HOME_AND_GARDEN = "Home & Garden";
    case VIDEO_GAMES = "Video Games";

    public static function name(): string
    {
        return 'Leisure';
    }
}
