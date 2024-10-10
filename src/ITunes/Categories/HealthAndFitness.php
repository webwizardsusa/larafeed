<?php

namespace Webwizardsusa\Larafeed\ITunes\Categories;

enum HealthAndFitness: string
{
    case ALTERNATIVE_HEALTH = "Alternative Health";
    case FITNESS = "Fitness";
    case MEDICINE = "Medicine";
    case MENTAL_HEALTH = "Mental Health";
    case NUTRITION = "Nutrition";
    case SEXUALITY = "Sexuality";

    public static function name(): string
    {
        return 'Health & Fitness';
    }
}
