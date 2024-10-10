<?php

namespace Webwizardsusa\Larafeed\Elements;

use Illuminate\Contracts\Support\Arrayable;
use Webwizardsusa\Larafeed\Contracts\Element;
use Webwizardsusa\Larafeed\Elements\Concerns\ElementHasAttributes;
use Webwizardsusa\Larafeed\Helpers\ElementCollection;

class FeedElement implements Element, Arrayable
{
    use ElementHasAttributes;
    protected string $tag;
    protected array|ElementCollection $children;

    public function __construct(string $tag, array|ElementCollection $children = [], array $attributes = [])
    {
        $this->tag = $tag;
        $this->attributes = $attributes;
        $this->children = $children;
    }

    public function addChild($element): static
    {
        $this->children[] = $element;

        return $this;
    }

    public function getTag(): string
    {
        return $this->tag;
    }

    public function setTag(string $tag): static
    {
        $this->tag = $tag;

        return $this;
    }

    public function getAttributes(): array
    {
        return $this->attributes;
    }

    public function setAttributes(array $attributes): static
    {
        $this->attributes = $attributes;

        return $this;
    }

    public function getChildren(): array|ElementCollection
    {
        return $this->children;
    }

    public function setChildren(array|ElementCollection $children): static
    {
        $this->children = $children;

        return $this;
    }

    public function toArray(): array
    {
        $children = collect($this->children)
            ->map(function ($child) {
                if ($child instanceof Arrayable) {
                    return $child->toArray();
                }

                return $child;
            });

        return [
            'tag' => $this->tag,
            'attributes' => $this->attributes,
            'children' => $children,
        ];
    }
}
