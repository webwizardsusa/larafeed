<?php

namespace Webwizardsusa\Larafeed\Concerns;

use Webwizardsusa\Larafeed\Contracts\Element;

trait HasExtraElements
{
    /** @var array | Element[] */
    protected array $extraElements = [];

    public function addElement(Element $element): static
    {
        $this->extraElements[] = $element;

        return $this;
    }

    /**
     * @return array|Element[]
     */
    public function getExtraElements(): array
    {
        return $this->extraElements;
    }
}
