<?php

namespace Webwizardsusa\Larafeed\Concerns;

use Webwizardsusa\Larafeed\BaseFeedItem;
use Webwizardsusa\Larafeed\Channel;
use Webwizardsusa\Larafeed\Exceptions\FeedValidationException;
use Webwizardsusa\Larafeed\ITunes\ITunesCategories;
use Webwizardsusa\Larafeed\PodcastItem;

/**
 * @mixin Channel
 */
trait ChannelSupportsITunes
{
    use HasITunesCategories;
    protected ?bool $explicit = null;



    protected ?string $iTunesImage = null;

    protected ?string $iTunesAuthor = null;



    protected ?string $iTunesTitle = null;


    protected bool $serial = false;

    public function bootChannelSupportsITunes(): void
    {
        $this->iTunesCategories = new ITunesCategories();
    }

    public function getExplicit(): ?bool
    {
        if ($this->explicit === null) {
            $this->explicit = $this->items->contains(fn (BaseFeedItem|PodcastItem $item) => $item instanceof PodcastItem && $item->isExplicit());
        }

        return $this->explicit;
    }

    public function setExplicit(?bool $explicit): static
    {
        $this->explicit = $explicit;

        return $this;
    }

    public function getITunesImage(): ?string
    {
        return $this->iTunesImage;
    }

    public function iTunesImage(?string $iTunesImage): static
    {
        $this->iTunesImage = $iTunesImage;

        return $this;
    }

    public function getITunesAuthor(): ?string
    {
        return $this->iTunesAuthor;
    }

    public function iTunesAuthor(?string $iTunesAuthor): static
    {
        $this->iTunesAuthor = $iTunesAuthor;

        return $this;
    }

    public function getITunesTitle(): ?string
    {
        return $this->iTunesTitle ?: $this->title;
    }

    public function iTunesTitle(?string $iTunesTitle): static
    {
        $this->iTunesTitle = $iTunesTitle;

        return $this;
    }

    public function serial(bool $isSerial = true): static
    {
        $this->serial = $isSerial;

        return $this;
    }

    public function isSerial(): bool
    {
        return $this->serial;
    }

    protected function validateITunes(): void
    {
        if (empty($this->iTunesCategories)) {
            throw new FeedValidationException('You must add at least one iTunes category.');
        }

        if (! $this->iTunesImage) {
            throw new FeedValidationException('You must add an iTunes image.');
        }
    }
}
