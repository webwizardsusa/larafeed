<?php

namespace Webwizardsusa\Larafeed\Fragments;

use Webwizardsusa\Larafeed\Exceptions\FeedValidationException;
use Webwizardsusa\Larafeed\Exceptions\LinkNotAbsoluteException;
use Webwizardsusa\Larafeed\Helpers\Utils;

class Enclosure
{
    protected string $url;
    protected int $length;
    protected string $type;

    public function __construct(string $url, int $length, ?string $type = null)
    {
        if (! $type) {
            $type = Utils::mimeFromFileName($url);
            if (! $type) {
                throw new FeedValidationException('Unable to determine mime type for enclosure.');
            }
        }
        $this->url = $url;
        $this->length = $length;
        $this->type = $type;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function url(string $url): static
    {
        $this->url = $url;

        return $this;
    }

    public function getLength(): int
    {
        return $this->length;
    }

    public function length(int $length): static
    {
        $this->length = $length;

        return $this;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function type(string $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function validate(): void
    {
        if (! Utils::isAbsoluteUrl($this->url)) {
            throw new LinkNotAbsoluteException('The enclosure URL for a feed item must be an absolute URL.');
        }
    }
}
