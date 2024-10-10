<?php

namespace Webwizardsusa\Larafeed\ITunes\Categories;

enum News: string
{
    case BUSINESS_NEWS = "Business News";
    case DAILY_NEWS = "Daily News";
    case ENTERTAINMENT_NEWS = "Entertainment News";
    case NEWS_COMMENTARY = "News Commentary";
    case POLITICS = "Politics";
    case SPORTS_NEWS = "Sports News";
    case TECH_NEWS = "Tech News";

    public static function name(): string
    {
        return 'News';
    }
}
