<?php

namespace Webwizardsusa\Larafeed\ITunes\Categories;

enum Business: string
{
    case CAREERS = "Careers";
    case ENTREPRENEURSHIP = "Entrepreneurship";
    case INVESTING = "Investing";
    case MANAGEMENT = "Management";
    case MARKETING = "Marketing";
    case NON_PROFIT = "Non-Profit";

    public static function name(): string
    {
        return 'Business';
    }
}
