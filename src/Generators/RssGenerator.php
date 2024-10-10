<?php

namespace Webwizardsusa\Larafeed\Generators;

use Carbon\Carbon;
use DOMDocument;
use DOMElement;
use Exception;
use Webwizardsusa\Larafeed\Channel;
use Webwizardsusa\Larafeed\Contracts\Element;
use Webwizardsusa\Larafeed\Elements\CDataElement;
use Webwizardsusa\Larafeed\Elements\FeedElement;
use Webwizardsusa\Larafeed\Elements\TextElement;

class RssGenerator extends AbstractGenerator
{
    protected Channel $channel;
    protected DOMDocument $document;

    public function __construct(Channel $channel)
    {
        parent::__construct($channel);
        $this->document = $this->build();
    }

    protected function createRssElement(DOMDocument $dom): DOMElement
    {
        $rss = $dom->createElement('rss');
        $rss->setAttribute('version', '2.0');
        $this->channel->namespaces->each(fn ($namespace, $prefix) => $rss->setAttribute("xmlns:$prefix", $namespace));

        return $rss;
    }

    public function build(): DOMDocument
    {
        $dom = new DOMDocument('1.0', 'UTF-8');
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = true;
        $rss = $this->createRssElement($dom);
        $dom->appendChild($rss);

        // Create the <channel> element
        $channel = $dom->createElement('channel');
        $rss->appendChild($channel);
        foreach ($this->channel->buildElements() as $element) {
            if ($child = $this->buildElement($element, $dom)) {
                $channel->appendChild($child);
            }
        }

        return $dom;
    }

    protected function buildElement(Element $element, DOMDocument $dom): ?DOMElement
    {
        if ($element instanceof CDataElement) {
            return $this->makeCdata($element, $dom);
        } elseif ($element instanceof FeedElement) {
            return $this->makeElement($element, $dom);
        } elseif ($element instanceof TextElement) {
            return $this->makeTextElement($element, $dom);
        } else {
            throw new Exception("Element type not supported");
        }

    }

    protected function makeElement(FeedElement $element, DOMDocument $dom): ?DOMElement
    {
        $el = $dom->createElement($element->getTag());
        foreach ($element->getAttributes() as $key => $value) {
            $el->setAttribute($key, $value);
        }

        foreach ($element->getChildren() as $child) {
            if ($childEl = $this->buildElement($child, $dom)) {
                $el->appendChild($childEl);
            }
        }

        return $el;
    }

    protected function normalizeContent(mixed $content): string
    {
        if ($content instanceof Carbon) {
            $content = $content->toRfc2822String();
        }

        return $content;
    }

    protected function makeCdata(CDataElement $element, DOMDocument $dom): ?DOMElement
    {
        $el = $dom->createElement($element->getTag());
        $cdata = $dom->createCDATASection($this->normalizeContent($element->getContent()));
        $el->appendChild($cdata);
        foreach ($element->getAttributes() as $key => $value) {
            $el->setAttribute($key, $value);
        }

        return $el;
    }

    protected function makeTextElement(TextElement $element, DOMDocument $dom): ?DOMElement
    {
        $el = $dom->createElement($element->getTag(), $this->normalizeContent($element->getContent()));
        foreach ($element->getAttributes() as $key => $value) {
            $el->setAttribute($key, $value);
        }

        return $el;
    }

    public function render(): string
    {
        return $this->document->saveXML();
    }

    public function __toString(): string
    {
        return $this->render();
    }

    public function contentType(): string
    {
        return config('larafeed.content_type', 'application/rss+xml; charset=UTF-8');
    }
}
