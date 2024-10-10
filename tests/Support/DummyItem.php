<?php

namespace Webwizardsusa\Larafeed\Test\Support;

use Webwizardsusa\Larafeed\BaseFeedItem;
use Webwizardsusa\Larafeed\Contracts\ProvidesFeedItem;
use Webwizardsusa\Larafeed\FeedItem;

class DummyItem implements ProvidesFeedItem
{
    public function toFeedItem(): BaseFeedItem
    {
        return FeedItem::make()
            ->title(fake()->sentence);
    }
}
