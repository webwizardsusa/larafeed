<?php

namespace Webwizardsusa\Larafeed\ITunes\Categories;

enum Education: string
{
    case COURSES = "Courses";
    case HOW_TO = "How To";
    case LANGUAGE_LEARNING = "Language Learning";
    case SELF_IMPROVEMENT = "Self-Improvement";

    public static function name(): string
    {
        return 'Education';
    }
}
