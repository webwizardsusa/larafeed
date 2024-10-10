<?php

namespace Webwizardsusa\Larafeed\Fragments;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Arr;
use Webwizardsusa\Larafeed\Exceptions\LinkNotAbsoluteException;
use Webwizardsusa\Larafeed\Helpers\Utils;

class FeedCategories implements Arrayable
{
    protected array $items = [];

    public function add(string $name, ?string $domain = null): self
    {
        $this->items[$name] = $domain;

        return $this;
    }

    public function remove(string $name): self
    {
        Arr::forget($this->items, $name);

        return $this;
    }

    public function all()
    {
        return collect($this->items)
            ->map(fn ($domain, $name) => ['name' => $name, 'domain' => $domain]);
    }

    public function validate(): void
    {
        foreach ($this->items as $category => $domain) {
            if ($domain && ! Utils::isAbsoluteUrl($domain)) {
                throw new LinkNotAbsoluteException('The domain for  feed item category' . $category . ' must be an absolute URL.');
            }
        }
    }

    public function toArray()
    {
        return collect($this->items)
            ->map(fn ($domain, $name) => ['name' => $name, 'domain' => $domain])
            ->values()
            ->toArray();
    }
}
