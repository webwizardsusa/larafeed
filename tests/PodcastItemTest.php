<?php

use Webwizardsusa\Larafeed\PodcastItem;

it('generates a podcast item', function () {
    $item = PodcastItem::make('https://test.com/test.mp4', 3000)
        ->duration(30)
        ->title('Test Title');
    $elements = $item->buildElements();
    expect($elements->findByTag('enclosure'))->toHaveCount(1)
        ->and($elements->findByTag('itunes:explicit'))->toHaveCount(1)
        ->and($elements->findByTag('itunes:duration'))->toHaveCount(1);
});

it('supports episodes', function () {
    $item = PodcastItem::make('https://test.com/test.mp4', 3000)
        ->duration(30)
        ->title('Test Title')
        ->episode(1)
        ->episodeSeason(2)
        ->episodeType(PodcastItem::EPISODE_TYPE_BONUS);
    $elements = $item->buildElements();
    expect($elements->findByTag('itunes:episodeType'))->toHaveCount(1)
        ->and($elements->findByTag('itunes:episode'))->toHaveCount(1)
        ->and($elements->findByTag('itunes:season'))->toHaveCount(1);
});

it('supports iTunes title', function () {
    $item = PodcastItem::make('https://test.com/test.mp4', 3000)
        ->duration(30)
        ->mediaTitle(fake()->word);

    $elements = $item->buildElements();
    expect($elements->findByTag('itunes:title'))->toHaveCount(1)
        ->and($elements->findByTag('title'))->toHaveCount(1);
});
