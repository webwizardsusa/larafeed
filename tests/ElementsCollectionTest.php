<?php

use Webwizardsusa\Larafeed\Contracts\Element;
use Webwizardsusa\Larafeed\Elements\CDataElement;
use Webwizardsusa\Larafeed\Elements\TextElement;
use Webwizardsusa\Larafeed\Helpers\ElementCollection;

it('iterates over elements', function () {
    $collection = new ElementCollection([
        new TextElement('title', 'hello'),
        new TextElement('description', 'world'),
    ]);

    expect(count($collection))->toBe(2);
    $iterated = false;
    foreach ($collection as $element) {
        $iterated = true;
    }
    expect($iterated)->toBeTrue()
        ->and($collection[0])
        ->toBeInstanceOf(Element::class);

});

it('filters elements by tag', function () {
    $collection = new ElementCollection([
        new TextElement('title', 'hello'),
        new TextElement('description', 'world'),
    ]);

    $elements = $collection->findByTag('description');
    expect($elements)
        ->toBeArray()
        ->and(count($elements))
        ->toBe(1)
        ->and($elements[0])
        ->toBeInstanceOf(Element::class);
});

it('filters elements by callback', function () {
    $collection = new ElementCollection([
        new TextElement('title', 'hello'),
        new TextElement('description', 'world'),
    ]);

    $elements = $collection->findBy(fn (Element $element) => $element->getTag() === 'description');
    expect($elements)
        ->toBeArray()
        ->and(count($elements))
        ->toBe(1)
        ->and($elements[0])
        ->toBeInstanceOf(Element::class);
});

it('it removes an element', function () {
    $collection = new ElementCollection([
        new TextElement('title', 'hello'),
        new TextElement('description', 'world'),
    ]);

    $collection->remove($collection[1]);
    expect(count($collection))->toBe(1)
        ->and($collection[0]->getTag())->toBe('title');
});

it('removes elements by filter callback', function () {
    $collection = new ElementCollection([
        new TextElement('title', 'hello'),
        new TextElement('description', 'world'),
        new CDataElement('extra', 'body'),
    ]);

    $collection->remove(fn (Element $element) => $element->getTag() != 'description');
    expect(count($collection))->toBe(1)
        ->and($collection[0]->getTag())->toBe('description');
});

it('conditionally adds elements', function () {
    $collection = new ElementCollection();
    $collection->addIf('test', fn () => new TextElement('title', 'hello'));
    $collection->addIf(false, fn () => new TextElement('description', 'world'));

    expect(count($collection))->toBe(1)
       ->and($collection[0]->getTag())->toBe('title');
});

it('conditionally adds elements through closure', function () {
    $collection = new ElementCollection();
    $collection->addIf(fn () => 'test', fn () => new TextElement('title', 'hello'));
    $collection->addIf(fn () => false, fn () => new TextElement('description', 'world'));

    expect(count($collection))->toBe(1)
        ->and($collection[0]->getTag())->toBe('title');
});
