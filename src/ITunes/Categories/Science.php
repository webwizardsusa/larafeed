<?php

namespace Webwizardsusa\Larafeed\ITunes\Categories;

enum Science: string
{
    case ASTRONOMY = "Astronomy";
    case CHEMISTRY = "Chemistry";
    case EARTH_SCIENCES = "Earth Sciences";
    case LIFE_SCIENCES = "Life Sciences";
    case MATHEMATICS = "Mathematics";
    case NATURAL_SCIENCES = "Natural Sciences";
    case NATURE = "Nature";
    case PHYSICS = "Physics";
    case SOCIAL_SCIENCES = "Social Sciences";

    public static function name(): string
    {
        return 'Science';
    }
}
