<?php

namespace Webwizardsusa\Larafeed\ITunes\Categories;

enum Arts: string
{
    case BOOKS = "Books";
    case DESIGN = "Design";
    case FASHION_AND_BEAUTY = "Fashion & Beauty";
    case FOOD = "Food";
    case PERFORMING_ARTS = "Performing Arts";
    case VISUAL_ARTS = "Visual Arts";

    public static function name(): string
    {
        return 'Arts';
    }
}
