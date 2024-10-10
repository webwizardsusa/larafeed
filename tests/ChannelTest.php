<?php

use Carbon\Carbon;
use Webwizardsusa\Larafeed\Channel;
use Webwizardsusa\Larafeed\FeedItem;
use Webwizardsusa\Larafeed\PodcastItem;
use Webwizardsusa\Larafeed\Test\Support\DummyItem;

it('generates a channel', function () {
    $channel = new Channel(fake()->word, fake()->url, fake()->sentence);
    $elements = $channel->buildElements();
    expect($elements->findByTag('title'))->toHaveCount(1)
        ->and($elements->findByTag('link'))->toHaveCount(1)
        ->and($elements->findByTag('description'))->toHaveCount(1);
});

it('has items in a channel', function () {
    $items = [
        FeedItem::make()->title(fake()->word)->link(fake()->url)->description(fake()->sentence),
        FeedItem::make()->title(fake()->word)->link(fake()->url)->description(fake()->sentence),
    ];
    $channel = new Channel(fake()->word, fake()->url, fake()->sentence, $items);
    $elements = $channel->buildElements();
    expect($elements->findByTag('item'))->toHaveCount(2);
});

it('can build items from provides feed item contract', function () {
    $items = [
        new DummyItem(),
        new DummyItem(),
    ];

    $channel = new Channel(fake()->word, fake()->url, fake()->sentence, $items);
    $elements = $channel->buildElements();
    expect($elements->findByTag('item'))->toHaveCount(2);
});

it('populates pubDate from items', function () {
    $date1 = Carbon::now()->subDays(1);
    $date2 = Carbon::now()->subDays(2);
    $items = [
        FeedItem::make()->title(fake()->word)->link(fake()->url)->description(fake()->sentence)->pubDate($date1),
        FeedItem::make()->title(fake()->word)->link(fake()->url)->description(fake()->sentence)->pubDate($date2),
    ];
    $channel = new Channel(fake()->word, fake()->url, fake()->sentence, $items);
    $elements = $channel->buildElements();
    expect($elements->findByTag('pubDate'))->toHaveCount(1)
        ->and($elements->findByTag('pubDate')[0]->getContent())->toBe($date1);
});

it('properly detects when feed has podcast items', function () {
    $channel = new Channel(fake()->word, fake()->url, fake()->sentence);
    expect($channel->hasMedia())->toBeFalse();
    $items = [
        new DummyItem(),
        new DummyItem(),
        PodcastItem::make('https://test.com/test.mp4', 1000)
            ->title(fake()->word)->link(fake()->url)->description(fake()->sentence),
    ];
    $channel->items($items);
    expect($channel->hasMedia())->toBeTrue();
});

it('ensures a feed link is absolute', function () {
    $channel = new Channel(fake()->word, fake()->word, fake()->sentence);
    $this->expectException(\Webwizardsusa\Larafeed\Exceptions\LinkNotAbsoluteException::class);
    $channel->buildElements();
});

it('returns a rss response', function () {
    $items = [
        new DummyItem(),
        new DummyItem(),
    ];

    $channel = new Channel(fake()->word, fake()->url, fake()->sentence, $items);
    $response = $channel->toResponse(request());
    expect($response)->toBeInstanceOf(\Illuminate\Http\Response::class)
    ->and($response->headers->get('Content-Type'))->toBe('application/rss+xml; charset=UTF-8')
    ->and($response->getContent())
        ->toBeString()
        ->toContain('<item>', '<title>', '<link>', '<description>', '</item>', '</channel>', '</rss>');
});
