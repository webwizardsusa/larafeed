<?php

namespace Webwizardsusa\Larafeed\ITunes\Categories;

enum KidsAndFamily: string
{
    case EDUCATION_FOR_KIDS = "Education for Kids";
    case PARENTING = "Parenting";
    case PETS_AND_ANIMALS = "Pets & Animals";
    case STORIES_FOR_KIDS = "Stories for Kids";

    public static function name(): string
    {
        return 'Kids & Family';
    }
}
