<?php

namespace Webwizardsusa\Larafeed\ITunes\Categories;

enum ReligionAndSpirituality: string
{
    case BUDDHISM = "Buddhism";
    case CHRISTIANITY = "Christianity";
    case HINDUISM = "Hinduism";
    case ISLAM = "Islam";
    case JUDAISM = "Judaism";
    case RELIGION = "Religion";
    case SPIRITUALITY = "Spirituality";

    public static function name(): string
    {
        return 'Religion & Spirituality';
    }
}
