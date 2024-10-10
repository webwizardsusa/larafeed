<?php

namespace Webwizardsusa\Larafeed\Elements\Concerns;

use Arr;

trait ElementHasAttributes
{
    protected array $attributes;

    public function getAttributes(): array
    {
        return $this->attributes;
    }

    public function setAttributes(array $attributes): static
    {
        $this->attributes = $attributes;

        return $this;
    }

    public function addAttribute(string $key, string $value): static
    {
        $this->attributes[$key] = $value;

        return $this;
    }

    public function getAttribute(string $key): ?string
    {
        return $this->attributes[$key] ?? null;
    }

    public function removeAttribute(string $key): static
    {
        Arr::forget($this->attributes, $key);

        return $this;
    }
}
