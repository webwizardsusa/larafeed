<?php

namespace Webwizardsusa\Larafeed\ITunes\Categories;

enum SocietyAndCulture: string
{
    case DOCUMENTARY = "Documentary";
    case PERSONAL_JOURNALS = "Personal Journals";
    case PHILOSOPHY = "Philosophy";
    case PLACES_AND_TRAVEL = "Places & Travel";
    case RELATIONSHIPS = "Relationships";

    public static function name(): string
    {
        return 'Society & Culture';
    }
}
