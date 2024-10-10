<?php

namespace Webwizardsusa\Larafeed;

use Webwizardsusa\Larafeed\Elements\FeedElement;
use Webwizardsusa\Larafeed\Elements\TextElement;
use Webwizardsusa\Larafeed\Exceptions\FeedValidationException;
use Webwizardsusa\Larafeed\Exceptions\LinkNotAbsoluteException;
use Webwizardsusa\Larafeed\Helpers\ElementCollection;
use Webwizardsusa\Larafeed\Helpers\Utils;

class PodcastItem extends BaseFeedItem
{
    public const EPISODE_TYPE_FULL = 'full';
    public const EPISODE_TYPE_TRAILER = 'trailer';

    public const EPISODE_TYPE_BONUS = 'bonus';
    protected string $view;
    protected ?int $duration = null;

    protected bool $explicit = false;


    protected ?string $image = null;

    protected ?string $episodeType = null;

    protected ?string $episodeSeason = null;

    protected ?string $episode = null;

    protected ?string $mediaTitle = null;

    public function __construct(string $enclosure, int $enclosureLength, ?string $enclosureType = null)
    {
        parent::__construct();
        $this->enclosure($enclosure, $enclosureLength, $enclosureType);
    }

    public static function make(string $enclosure, int $enclosureLength, ?string $enclosureType = null): static
    {
        return new static($enclosure, $enclosureLength, $enclosureType);

    }

    public function duration(?int $duration): static
    {
        $this->duration = $duration;

        return $this;
    }

    public function getDuration(): ?int
    {
        return $this->duration;
    }

    public function explicit(bool $explicit): static
    {
        $this->explicit = $explicit;

        return $this;
    }

    public function isExplicit(): bool
    {
        return $this->explicit;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function image(?string $image): PodcastItem
    {
        $this->image = $image;

        return $this;
    }

    public function getEpisodeType(): ?string
    {
        return $this->episodeType;
    }

    public function episodeType(?string $episodeType): static
    {
        $this->episodeType = $episodeType;

        return $this;
    }

    public function getEpisodeSeason(): ?string
    {
        return $this->episodeSeason;
    }

    public function episodeSeason(?string $episodeSeason): static
    {
        $this->episodeSeason = $episodeSeason;

        return $this;
    }

    public function getEpisode(): ?string
    {
        return $this->episode;
    }

    public function episode(?string $episode): static
    {
        $this->episode = $episode;

        return $this;
    }

    public function mediaTitle(?string $mediaTitle): static
    {
        $this->mediaTitle = $mediaTitle;

        return $this;
    }

    public function getMediaTitle(): ?string
    {
        return $this->mediaTitle;
    }

    protected function validate(): void
    {
        if (! $this->title && $this->mediaTitle) {
            $this->title = $this->mediaTitle;
        }
        parent::validate();
        if ($episodeType = $this->getEpisodeType()) {
            if (! in_array($episodeType, [self::EPISODE_TYPE_FULL, self::EPISODE_TYPE_TRAILER, self::EPISODE_TYPE_BONUS])) {
                throw new FeedValidationException('Episode type must be one of: full, trailer, bonus');
            }
        }
        $image = $this->getImage();
        throw_if($image && ! Utils::isAbsoluteUrl($image), LinkNotAbsoluteException::class, 'Image link must be absolute');
    }

    public function buildElements(): ElementCollection
    {
        $elements = parent::buildElements();
        $elements->add(new TextElement('itunes:explicit', $this->isExplicit() ? 'true' : 'false'));
        $elements->addIf($this->getMediaTitle(), fn ($value) => new TextElement('itunes:title', $value));
        $elements->addIf($this->getImage(), fn ($value) => new FeedElement(
            tag: 'itunes:image',
            attributes: ['href' => $value]
        ));
        $elements->addIf($this->getDuration(), fn ($value) => new TextElement('itunes:duration', $value));
        $elements->addIf($this->getEpisodeType(), fn ($value) => new TextElement('itunes:episodeType', $value));
        $elements->addIf($this->getEpisode(), fn ($value) => new TextElement('itunes:episode', $value));
        $elements->addIf($this->getEpisodeSeason(), fn ($value) => new TextElement('itunes:season', $value));

        return $elements;
    }
}
