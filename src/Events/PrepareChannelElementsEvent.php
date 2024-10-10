<?php

namespace Webwizardsusa\Larafeed\Events;

use Webwizardsusa\Larafeed\Channel;
use Webwizardsusa\Larafeed\Helpers\ElementCollection;

class PrepareChannelElementsEvent
{
    public ElementCollection $elements;
    public Channel $channel;

    public function __construct(ElementCollection $elements, Channel $channel)
    {
        $this->elements = $elements;
        $this->channel = $channel;
    }
}
