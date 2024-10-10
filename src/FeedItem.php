<?php

namespace Webwizardsusa\Larafeed;

class FeedItem extends BaseFeedItem
{
    public static function make(): static
    {
        return new static();
    }
}
