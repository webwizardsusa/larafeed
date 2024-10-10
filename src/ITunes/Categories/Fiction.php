<?php

namespace Webwizardsusa\Larafeed\ITunes\Categories;

enum Fiction: string
{
    case COMEDY_FICTION = "Comedy Fiction";
    case DRAMA = "Drama";
    case SCIENCE_FICTION = "Science Fiction";

    public static function name(): string
    {
        return 'Fiction';
    }
}
