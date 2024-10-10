<?php

namespace Webwizardsusa\Larafeed;

use Carbon\Carbon;
use Exception;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Foundation\Application;
use Illuminate\Support\Collection;
use Symfony\Component\HttpFoundation\Response;
use Webwizardsusa\Larafeed\Concerns\ChannelSupportsITunes;
use Webwizardsusa\Larafeed\Concerns\HasExtraElements;
use Webwizardsusa\Larafeed\Contracts\ProvidesFeedItem;
use Webwizardsusa\Larafeed\Elements\CDataElement;
use Webwizardsusa\Larafeed\Elements\FeedElement;
use Webwizardsusa\Larafeed\Elements\TextElement;
use Webwizardsusa\Larafeed\Exceptions\LinkNotAbsoluteException;
use Webwizardsusa\Larafeed\Generators\AbstractGenerator;
use Webwizardsusa\Larafeed\Generators\RssGenerator;
use Webwizardsusa\Larafeed\Helpers\ElementCollection;
use Webwizardsusa\Larafeed\Helpers\Utils;

class Channel implements Arrayable, Responsable
{
    use ChannelSupportsITunes;
    use HasExtraElements;

    /**
     * @var Collection | BaseFeedItem[]
     */
    public Collection|array $items;
    protected string $title;
    protected string $link;
    protected string $description;

    protected ?string $language = 'en-us';

    public ?string $managingEditorName = null;

    public ?string $managingEditorEmail = null;

    public ?string $webmasterName = null;

    public ?string $webmasterEmail = null;

    public ?Carbon $pubDate = null;

    public ?Carbon $lastBuildDate = null;

    public ?string $generator = null;


    public ?int $ttl = null;

    public ?string $image = null;

    public ?string $webMasterName = null;

    public ?string $webMasterEmail = null;

    public ?string $copyright = null;
    protected ?string $imageTitle = null;

    protected ?string $imageLink = null;



    public Collection $namespaces;

    protected ?AbstractGenerator $generatorInstance = null;

    protected string $generatorClass;

    protected ?string $contentType = null;

    public Collection $headers;

    public function __construct(string $title, string $link, string $description, array|Collection $items = [])
    {

        $this->headers = collect(config('larafeed.extra_headers', []));
        $this->language = config('larafeed.channel_language', 'en-us');
        $this->namespaces = collect(config('larafeed.namespaces', []));
        $this->generatorClass = config('larafeed.generator', RssGenerator::class);
        $this->items = collect();
        collect($items)->each(fn ($item) => $this->addItem($item));
        $this->title = $title;
        $this->link = $link;
        $this->description = $description;
        foreach (class_uses_recursive(static::class) as $trait) {
            if (method_exists($this, $method = 'boot' . class_basename($trait))) {
                $this->$method();
            }
        }
    }

    public static function make(string $title, string $link, string $description, array|Collection $items = []): static
    {
        return new static($title, $link, $description, $items);
    }

    public function items(array|Collection $items): static
    {
        $this->items = collect($items);

        return $this;
    }

    public function addItem(BaseFeedItem|ProvidesFeedItem $item): static
    {
        if ($item instanceof ProvidesFeedItem) {
            $item = $item->toFeedItem();
        } elseif (! ($item instanceof BaseFeedItem)) {
            throw new Exception('A feed item must be an instance of ' . BaseFeedItem::class . ' or implement ' . ProvidesFeedItem::class . '.');
        }

        $this->items->push($item->channel($this));

        return $this;
    }

    public function title(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function link(string $link): static
    {
        $this->link = $link;

        return $this;
    }

    public function description(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function language(?string $language): static
    {
        $this->language = $language;

        return $this;
    }

    public function generator(?string $generator): static
    {
        $this->generator = $generator;

        return $this;
    }

    public function lastBuildDate(?Carbon $lastBuildDate): static
    {
        $this->lastBuildDate = $lastBuildDate;

        return $this;
    }

    public function ttl(?int $ttl): static
    {
        $this->ttl = $ttl;

        return $this;
    }

    public function image(?string $image, ?string $title = null, ?string $link = null): static
    {
        $this->image = $image;
        $this->imageTitle = $title;
        $this->imageLink = $link;

        return $this;
    }

    public function imageTitle(?string $title): static
    {
        $this->imageTitle = $title;

        return $this;
    }

    public function imageLink(?string $link): static
    {
        $this->imageLink = $link;

        return $this;
    }

    public function pubDate(null|string|Carbon $pubDate): static
    {
        $this->pubDate = $pubDate;

        return $this;
    }

    public function editor(string $email, ?string $name = null): static
    {
        $this->managingEditorEmail = $email;
        $this->managingEditorName = $name;

        return $this;
    }

    public function webmaster(string $email, ?string $name = null): static
    {
        $this->webmasterEmail = $email;
        $this->webmasterName = $name;

        return $this;
    }

    public function copyright(?string $copyright): static
    {
        $this->copyright = $copyright;

        return $this;
    }

    public function getItems(): Collection
    {
        return $this->items;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getLink(): string
    {
        return $this->link;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getLanguage(): ?string
    {
        return $this->language;
    }

    public function getManagingEditorName(): ?string
    {
        return $this->managingEditorName;
    }

    public function getManagingEditorEmail(): ?string
    {
        return $this->managingEditorEmail;
    }

    public function getManagingEditor(): ?string
    {
        return Utils::formatUserAndEmail($this->getManagingEditorEmail(), $this->getManagingEditorName());
    }

    public function getWebmasterName(): ?string
    {
        return $this->webmasterName;
    }

    public function getWebmasterEmail(): ?string
    {
        return $this->webmasterEmail;
    }

    public function getWebmaster(): ?string
    {
        return Utils::formatUserAndEmail($this->getWebMasterEmail(), $this->getWebMasterName());
    }

    public function getPubDate(): ?Carbon
    {
        if (! $this->pubDate && $this->items->isNotEmpty()) {
            $this->pubDate = $this->items->sortByDesc('getPubDate')->first()->getPubDate();
        }

        return $this->pubDate ?: Carbon::now();
    }

    public function getLastBuildDate(): ?Carbon
    {
        return $this->lastBuildDate;
    }

    public function getGenerator(): ?string
    {
        return $this->generator;
    }

    public function getTtl(): ?int
    {
        return $this->ttl;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function getImageTitle(): ?string
    {
        return $this->imageTitle;
    }

    public function getImageLink(): ?string
    {
        return $this->imageLink;
    }

    public function getCopyright(): ?string
    {
        return $this->copyright;
    }

    public function hasMedia(): bool
    {
        return $this->items->contains(fn ($item) => $item instanceof PodcastItem);
    }

    public function getContentType(): string
    {
        return $this->contentType;
    }

    public function contentType(string $contentType): static
    {
        $this->contentType = $contentType;

        return $this;
    }

    public function toArray(): array
    {
        return $this->buildElements()->toArray();
    }

    public function validate(): void
    {
        if ($this->hasMedia()) {
            $this->validateITunes();
        }
        throw_if(! Utils::isAbsoluteUrl($this->getLink()), LinkNotAbsoluteException::class, 'Channel links must be absolute.');
    }

    public function buildElements(): ElementCollection
    {
        $this->validate();
        $elements = new ElementCollection([
            new TextElement('title', $this->getTitle()),
            new TextElement('link', $this->getLink()),
            new CDataElement('description', $this->getDescription()),
            new TextElement('language', $this->getLanguage()),
            new TextElement('pubDate', $this->getPubDate()),
        ]);
        $elements->addIf($this->namespaces->has('atom'), fn () => new TextElement('atom:link', '', [
            'href' => $this->getLink(),
            'rel' => 'self',
            'type' => 'application/rss+xml',
        ]));
        $elements->addIf($this->getTtl(), fn ($value) => new TextElement('ttl', $value));
        $elements->addIf($this->getGenerator(), fn ($value) => new TextElement('generator', $value));
        $elements->addIf($this->getCopyright(), fn ($value) => new TextElement('copyright', $value));
        $elements->addIf($this->getManagingEditor(), fn ($value) => new TextElement('managingEditor', $value));
        $elements->addIf($this->getWebmaster(), fn ($value) => new TextElement('webMaster', $value));

        $elements->addIf($this->image, fn () => new FeedElement('image', [
            new TextElement('url', $this->image),
            new TextElement('title', $this->imageTitle ?: $this->getTitle()),
            new TextElement('link', $this->imageLink ?: $this->getLink()),
        ]));

        if ($this->hasMedia()) {
            $this->addItunesElements($elements);
        }

        $elements->add($this->extraElements);

        foreach ($this->items as $item) {
            $elements->add(new FeedElement('item', $item->buildElements()));
        }

        return $elements;
    }

    protected function addItunesElements(ElementCollection $elements): static
    {
        $elements->add(new TextElement('itunes:explicit', $this->getExplicit() ? 'true' : 'false'));
        $elements->addIf($this->getITunesAuthor(), fn ($value) => new CDataElement('itunes:author', $value));
        $elements->addIf($this->isSerial(), fn () => new TextElement('itunes:type', 'serial'));

        $elements->addIf($this->getITunesImage(), fn ($value) => new FeedElement(
            tag: 'itunes:image',
            attributes: ['href' => $value]
        ));
        foreach ($this->getITunesCategories() as $category) {
            $cat = new FeedElement(
                tag: 'itunes:category',
                attributes: ['text' => $category['category']]
            );
            foreach ($category['children'] as $child) {
                $cat->addChild(new FeedElement(
                    tag: 'itunes:category',
                    attributes: ['text' => $child]
                ));
            }
            $elements->add($cat);
        }

        return $this;
    }

    public function build(bool $forceRecreate = false): AbstractGenerator
    {
        if ($forceRecreate || ! $this->generatorInstance) {
            $this->generatorInstance = app($this->generatorClass, ['channel' => $this]);
        }

        return $this->generatorInstance;
    }

    public function render(): string
    {

        return $this->build()->render();
    }

    public function save(string $path, ?string $disk = null): static
    {
        $this->build()->save($path, $disk);

        return $this;
    }

    public function toResponse($request): Application|\Illuminate\Http\Response|Response|ResponseFactory
    {
        return response($this->render(), 200, array_merge([
            'Content-Type' => $this->generatorInstance->contentType(),
        ], $this->headers->toArray()));
    }
}
