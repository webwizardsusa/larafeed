<?php

namespace Webwizardsusa\Larafeed\ITunes\Categories;

enum Comedy: string
{
    case COMEDY_INTERVIEWS = "Comedy Interviews";
    case IMPROV = "Improv";
    case STAND_UP = "Stand-Up";

    public static function name(): string
    {
        return 'Comedy';
    }
}
