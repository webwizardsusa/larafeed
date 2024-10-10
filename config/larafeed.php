<?php

use Webwizardsusa\Larafeed\Generators\RssGenerator;

return [
    // This is the class for default generation.
    'generator' => RssGenerator::class,

    // This is the default content-type header added to responses. Generators can override this.
    'content_type' => 'application/rss+xml; charset=UTF-8',

    // If you want to add any extra response headers, you can.
    'extra_headers' => [],

    // This is the default lists of namespaces to be added to channels.
    'namespaces' => [
        "content" => "http://purl.org/rss/1.0/modules/content/",
        "wfw" => "http://wellformedweb.org/CommentAPI/",
        "dc" => "http://purl.org/dc/elements/1.1/",
        "atom" => "http://www.w3.org/2005/Atom",
        "itunes" => "http://www.itunes.com/dtds/podcast-1.0.dtd",
        "sy" => "http://purl.org/rss/1.0/modules/syndication/",
        "slash" => "http://purl.org/rss/1.0/modules/slash/",
        "media" => "http://search.yahoo.com/mrss/",
    ]
];
