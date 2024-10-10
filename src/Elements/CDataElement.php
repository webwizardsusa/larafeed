<?php

namespace Webwizardsusa\Larafeed\Elements;

use Carbon\Carbon;
use Illuminate\Contracts\Support\Arrayable;
use Webwizardsusa\Larafeed\Contracts\Element;
use Webwizardsusa\Larafeed\Elements\Concerns\ElementHasAttributes;

class CDataElement implements Element, Arrayable
{
    use ElementHasAttributes;
    protected string $tag;
    protected string|Carbon $content;

    public function __construct(string $tag, string|Carbon $content, array $attributes = [])
    {
        $this->tag = $tag;
        $this->content = $content;
        $this->attributes = $attributes;
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

    public function getContent(): Carbon|string
    {
        return $this->content;
    }

    public function setContent(Carbon|string $content): static
    {
        $this->content = $content;

        return $this;
    }

    public function toArray(): array
    {
        return [
            'tag' => $this->tag,
            'content' => $this->content,
            'attributes' => $this->attributes,
        ];
    }
}
