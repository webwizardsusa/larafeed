<?php

namespace Webwizardsusa\Larafeed\ITunes\Categories;

enum Sports: string
{
    case BASEBALL = "Baseball";
    case BASKETBALL = "Basketball";
    case CRICKET = "Cricket";
    case FANTASY_SPORTS = "Fantasy Sports";
    case FOOTBALL = "Football";
    case GOLF = "Golf";
    case HOCKEY = "Hockey";
    case RUGBY = "Rugby";
    case RUNNING = "Running";
    case SOCCER = "Soccer";
    case SWIMMING = "Swimming";
    case TENNIS = "Tennis";
    case VOLLEYBALL = "Volleyball";
    case WILDERNESS = "Wilderness";
    case WRESTLING = "Wrestling";

    public static function name(): string
    {
        return 'Sports';
    }
}
