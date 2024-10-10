# RSS Feeds For Laravel

This package provides advanced RSS style feeds, including support for Podcast/ITunes feeds.

## Installation

You can install the package via composer:

```
composer require webwizardsusa/larafeed
```

### Publishing the configuration (optional)

```
php artisan vendor:publish --provider="Webwizardsusa\Larafeed\LarafeedServiceProvider" --tag="larafeed"
```

## Usage

Generating a feed is extremely simple. Let's say you have a model calls Post and want to supply an RSS feed of that. You
simply add the interface `Webwizardsusa\Larafeed\Contracts\ProvidesFeedItem` to your model and define the public method
toFeedItem():

Models\Post

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Webwizardsusa\Larafeed\BaseFeedItem;
use Webwizardsusa\Larafeed\Contracts\ProvidesFeedItem;
use Webwizardsusa\Larafeed\FeedItem;

class Post extends Model implements ProvidesFeedItem
{
    use HasFactory;

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    public function toFeedItem(): BaseFeedItem
    {
        return FeedItem::make()
            ->link(url('post/' . $this->id))
            ->title($this->title)
            ->author($this->user?->name)
            ->content($this->body)
            ->pubDate($this->created_at);
    }
}

```

Next create your controller:

```php
    public function __invoke(Request $request)
    {
        return \Webwizardsusa\Larafeed\Channel::make('Test Feed', $request->fullUrl(), 'Our first feed', Post::query()
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get());
    }
```

And that is all. The route you set to that controller will have a valid RSS feed.

Of course, you aren't limited to feed items from models. Any class that implements `ProvidesFeedItem` can supply a feed
item. You can also pass an array or collection of `FeedItem` and/or `PodcastItem `to the 4th parameter on
`Channel::make`.

### ITunes Support

This package provides support for iTunes out of the box, including iTunes categories. There are classes for all defined
iTunes categories in `ITunes/Categories`. If you add a child of a category, the parent category will automatically be
added. For example:

```php
$channel->iTunesCategory(\Webwizardsusa\Larafeed\ITunes\Categories\Sports::RUNNING);
```

Will automatically add:

```xml

<itunes:category text="Sports">
    <itunes:category text="Running"/>
</itunes:category>
```

to the channel of the feed.

### Validation

When creating a feed, there are validation methods for both feed items and the channel. For example, if your feed
contains a PodCast item, then the itunesImage must be set on the channel. All urls are also checked to be absolute.
Other validation rules from both the [RSS feed specification](https://www.rssboard.org/rss-specification) and [iTunes feed specification](https://podcasters.apple.com/support/823-podcast-requirements) are checked. If any of these
fail during building, then an exception is thrown.

## Extensibility

This package was written with extensibility in mind. You can create different types of feed items. Change the RSS name
spaces. You can even generate a totally different style feed, such as JSON, via the use of generators. To help with
this, here's a basic description of the various parts of this package:

### Channel

A channel is either `Webwizardsusa\Larafeed\Channel` or a class that extends that. The channel handles generating the
entire feed, via generators. It can also supply a response, so you can simply return a channel from a controller.

### FeedItems

A feed item is a class that extends `Webwizardsusa\Larafeed\BaseFeedItem`. By default, we supply two types of feed item
classes:

1. `Webwizardsusa\Larafeed\FeedItem` - this is a basic feed item.
2. `Webwizardsusa\Larafeed\PodcastItem` - this is a feed item that includes iTunes/Podcast information.

All feed items are validated when building. For example, it checks that URLs are absolute.

**Enclosures:**

Enclosures are a way to supply media to a feed item. For a basic feed, this could be an image that is displayed in the
feed reader. For a PodcastItem, this is required and will be the link to the actual media item. Enclosures do require a
mimetype. If your enclosure URL has a proper extension, then the system will try to determine the mimetype from that. If
not, or if you want to force a mimetype, you can supply it to the enclosure.

### Elements

When a channel or feed item is built, it generates a `\Webwizardsusa\Larafeed\HelpersElementCollection`. This is a
custom collection to work with the parts of channels and feeds. Elements are simple classes that implement
`\Webwizardsusa\Larafeed\Contracts\Element`.

### Generators

Once the element tree is built, then the channel can be rendered. For this, we use generators. Currently there is only a
single generator, `\Webwizardsusa\Larafeed\Generators\RssGenerator`. This uses DOMDocument to generate a valid and
well-formed RSS feed.

Generators are assigned to Channels, so if you wish to offer something like a JSON feed, you can do so by creating a new
generator and telling the channel to use that, with   `$channel->generator({generator class name})`.

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](https://github.com/spatie/.github/blob/main/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

This package is developed by [WebWizardsUSA](https://webwizardsusa.com/).

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.