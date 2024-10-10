<?php

namespace Webwizardsusa\Larafeed\ITunes\Categories;

enum TVAndFilm: string
{
    case AFTER_SHOWS = "After Shows";
    case FILM_HISTORY = "Film History";
    case FILM_INTERVIEWS = "Film Interviews";
    case FILM_REVIEWS = "Film Reviews";
    case T_V_REVIEWS = "TV Reviews";

    public static function name(): string
    {
        return 'TV & Film';
    }
}
