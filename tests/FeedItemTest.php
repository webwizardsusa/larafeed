<?php

use Webwizardsusa\Larafeed\Exceptions\LinkNotAbsoluteException;
use Webwizardsusa\Larafeed\Exceptions\MissingRequiredItemFieldException;
use Webwizardsusa\Larafeed\FeedItem;
use Webwizardsusa\Larafeed\Helpers\ElementCollection;

test("it can create a feed item", function () {
    $item = FeedItem::make()
        ->title('Test Item');
    expect($item->toArray())->toHaveCount(1);
});

test('it validates a feed item', function () {
    $this->expectException(MissingRequiredItemFieldException::class);
    $item = FeedItem::make();
    $item->toArray();
});

test('it ensures item link is absolute', function () {
    $item = FeedItem::make()
        ->title(fake()->sentence)
        ->link('Test Item');
    $this->expectException(LinkNotAbsoluteException::class);
    $item->buildElements();
});

test('it ensures enclosure link is absolute', function () {
    $item = FeedItem::make()
        ->title(fake()->sentence)
        ->enclosure('Test Item', 100, 'audio/mpeg');
    $this->expectException(LinkNotAbsoluteException::class);
    $item->buildElements();
});

test('it ensures guids are absolute when set to permalink', function () {
    $item = FeedItem::make()
        ->title(fake()->sentence)
        ->guid(1);
    expect($item->buildElements())->toBeInstanceOf(ElementCollection::class);

    $item->guid('bad', true);

    $this->expectException(LinkNotAbsoluteException::class);
    $item->buildElements();
});

test('it ensures source urls are absolute', function () {
    $item = FeedItem::make()
        ->title(fake()->sentence)
        ->source('Test Item', 'test');
    $this->expectException(LinkNotAbsoluteException::class);
    $item->buildElements();
});

test('it can have a source', function () {
    $item = FeedItem::make()
        ->title(fake()->sentence)
        ->source('Test Item', fake()->url);

    $elements = $item->buildElements();
    expect($elements->findByTag('source'))
        ->toHaveCount(1)
        ->and($elements->findByTag('source')[0]->getAttribute('url'))
        ->toBeString();

});

test('it properly handles authors and creators', function () {
    $item = FeedItem::make()
        ->author(fake()->name)
        ->title('Test Item');

    $elements = $item->buildElements();

    expect($elements->findByTag('dc:creator'))->toHaveCount(1)
        ->and($elements->findByTag('author'))->toBeEmpty();

    $item->author(fake()->name, fake()->email);

    $elements = $item->buildElements();
    expect($elements->findByTag('dc:creator'))->toHaveCount(1)
        ->and($elements->findByTag('author'))->toHaveCount(1);

    $item->creator(fake()->name);

    $elements = $item->buildElements();
    expect($elements->findByTag('dc:creator'))->toHaveCount(2)
        ->and($elements->findByTag('author'))->toHaveCount(1);
});

test('it handles feed item categories', function () {
    $item = FeedItem::make()
        ->author(fake()->name)
        ->title('Test Item')
        ->addCategory(fake()->word)
        ->addCategory(fake()->word, fake()->url);

    $elements = $item->buildElements();

    $categories = $elements->findByTag('category');
    expect($categories)->toHaveCount(2);
    $domainCount = 0;
    foreach ($categories as $category) {
        if ($category->getAttribute('domain')) {
            $domainCount++;
        }
    }
    expect($domainCount)->toBe(1);
});
