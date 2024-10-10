<?php

namespace Webwizardsusa\Larafeed;

use Carbon\Carbon;
use Illuminate\Contracts\Support\Arrayable;
use Webwizardsusa\Larafeed\Concerns\HasExtraElements;
use Webwizardsusa\Larafeed\Elements\CDataElement;
use Webwizardsusa\Larafeed\Elements\FeedElement;
use Webwizardsusa\Larafeed\Elements\TextElement;
use Webwizardsusa\Larafeed\Exceptions\LinkNotAbsoluteException;
use Webwizardsusa\Larafeed\Exceptions\MissingRequiredItemFieldException;
use Webwizardsusa\Larafeed\Fragments\Enclosure;
use Webwizardsusa\Larafeed\Fragments\FeedCategories;
use Webwizardsusa\Larafeed\Helpers\ElementCollection;
use Webwizardsusa\Larafeed\Helpers\Utils;

abstract class BaseFeedItem implements Arrayable
{
    use HasExtraElements;

    protected ?string $title = null;

    protected ?string $link = null;

    protected ?string $description = null;

    protected ?string $authorName = null;

    protected ?string $authorEmail = null;

    protected Carbon|string|null $pubDate = null;

    protected ?string $guid = null;

    protected bool $isGuidPermalink = false;

    protected ?Enclosure $enclosure = null;


    public FeedCategories $categories;

    public ?Channel $channel = null;

    protected array $creators = [];

    protected ?string $content = null;

    protected ?string $source = null;
    protected ?string $sourceUrl = null;

    public function __construct()
    {
        $this->categories = new FeedCategories();
    }

    public function channel(?Channel $channel): self
    {
        $this->channel = $channel;

        return $this;
    }

    public function title(?string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function link(?string $link): self
    {
        $this->link = $link;

        return $this;
    }

    public function description(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function content(?string $content): static
    {
        $this->content = $content;

        return $this;
    }

    public function author(?string $authorName, ?string $authorEmail = null): self
    {
        $this->authorEmail = $authorEmail;
        $this->authorName = $authorName;

        return $this;
    }

    public function authorName(?string $authorName): self
    {
        $this->authorName = $authorName;

        return $this;
    }

    public function authorEmail(?string $authorEmail): self
    {
        $this->authorEmail = $authorEmail;

        return $this;
    }

    public function creator(string $name): static
    {
        if (! in_array($name, $this->creators)) {
            $this->creators[] = $name;
        }

        return $this;
    }

    public function removeCreator(string $name): static
    {
        $this->creators = array_filter($this->creators, fn ($creator) => $creator !== $name);

        return $this;
    }

    public function pubDate(Carbon|string|null $pubDate): self
    {
        $this->pubDate = $pubDate;

        return $this;
    }

    public function guid(?string $guid, bool $isPermalink = false): self
    {
        $this->guid = $guid;
        $this->isGuidPermalink = $isPermalink;

        return $this;
    }

    public function isGuidPermalink(): bool
    {
        return $this->isGuidPermalink;
    }

    public function enclosure(?string $enclosure, int $enclosureLength = 0, ?string $enclosureType = null): self
    {
        if ($enclosure && $enclosureLength) {
            $this->enclosure = new Enclosure($enclosure, $enclosureLength, $enclosureType);
        } else {
            $this->enclosure = null;
        }

        return $this;
    }

    public function source(?string $source, ?string $sourceUrl = null): self
    {
        $this->source = $source;
        $this->sourceUrl = $sourceUrl;

        return $this;
    }

    public function getSource(): ?string
    {
        return $this->source;
    }

    public function getSourceUrl(): ?string
    {
        return $this->sourceUrl;
    }

    public function hasSource(): bool
    {
        return $this->source && $this->sourceUrl;
    }

    public function addCategory(string $category, ?string $domain = null): self
    {
        $this->categories->add($category, $domain);

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function getLink(): ?string
    {
        return $this->link;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getAuthorName(): ?string
    {
        return $this->authorName;
    }

    public function getAuthorEmail(): ?string
    {
        return $this->authorEmail;
    }

    public function getAuthor(): ?string
    {
        return Utils::formatUserAndEmail($this->getAuthorEmail(), $this->getAuthorName());
    }

    public function getPubDate(): null|Carbon
    {
        return $this->pubDate;
    }

    public function getGuid(): ?string
    {
        return $this->guid;
    }

    public function getEnclosure(): ?Enclosure
    {
        return $this->enclosure;
    }

    public function getCategories(): FeedCategories
    {
        return $this->categories;
    }

    public function getChannel(): ?Channel
    {
        return $this->channel;
    }

    public function toArray(): array
    {
        return $this->buildElements()->toArray();
    }

    protected function validate(): void
    {
        if (! $this->getDescription() && ! $this->getTitle()) {
            throw new MissingRequiredItemFieldException('A feed item must have a title or description.');
        }
        $link = $this->getLink();
        if ($link && ! Utils::isAbsoluteUrl($link)) {
            throw new LinkNotAbsoluteException('The link for a feed item must be an absolute URL.');
        }

        $guid = $this->getGuid();
        if ($guid && $this->isGuidPermalink() && ! Utils::isAbsoluteUrl($guid)) {
            throw new LinkNotAbsoluteException('The guid for a feed item must be an absolute URL when set to permalink.');
        }


        if ($this->getSource() && $url = $this->getSourceUrl()) {
            if (! Utils::isAbsoluteUrl($url)) {
                throw new LinkNotAbsoluteException('The source url for a feed item must be an absolute URL.');
            }
        }

        $this->getEnclosure()?->validate();
        $this->categories->validate();
    }

    public function buildElements(): ElementCollection
    {
        $this->validate();
        $creators = $this->creators;
        if ($this->getAuthorName() && ! in_array($this->getAuthorName(), $creators)) {
            array_unshift($creators, $this->getAuthorName());
        }
        $elements = new ElementCollection();
        $elements->addIf($this->getTitle(), fn ($value) => new TextElement('title', $value))
            ->addIf($this->getLink(), fn ($value) => new TextElement('link', $value))
            ->addIf($this->getDescription(), fn ($value) => new CDataElement('description', $value))
            ->addIf($this->getContent(), fn ($value) => new CDataElement('content:encoded', $value))
            ->addIf($this->getPubDate(), fn ($value) => new TextElement('pubDate', $value))
            ->addIf($this->getAuthor(), fn ($value) => new TextElement('author', $value))
            ->addIf($this->getGuid(), function ($value) {
                $attributes = [];
                if ($this->isGuidPermalink()) {
                    $attributes['permalink'] = true;
                }

                return new TextElement('guid', $value, $attributes);
            });

        if (! empty($creators)) {
            foreach ($creators as $creator) {
                $elements->add(new TextElement('dc:creator', $creator));
            }
        }

        $elements->addIf($this->getEnclosure(), fn (Enclosure $enclosure) => new FeedElement(
            tag: 'enclosure',
            attributes: [
                'url' => $enclosure->getUrl(),
                'type' => $enclosure->getType(),
                'length' => $enclosure->getLength(),
            ]
        ));

        $elements->addIf($this->hasSource(), fn () => new TextElement('source', $this->getSource(), [
            'url' => $this->getSourceUrl(),
        ]));

        foreach ($this->getCategories()->all() as $category) {
            $attributes = [];
            if ($category['domain']) {
                $attributes['domain'] = $category['domain'];
            }
            $elements->add(new TextElement('category', $category['name'], $attributes));
        }

        $elements->add($this->extraElements);

        return $elements;
    }
}
