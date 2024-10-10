<?php

namespace Webwizardsusa\Larafeed\Contracts;

use Webwizardsusa\Larafeed\BaseFeedItem;

interface ProvidesFeedItem
{
    public function toFeedItem(): BaseFeedItem;
}
