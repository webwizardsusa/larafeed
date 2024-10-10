<?php

namespace Webwizardsusa\Larafeed\Concerns;

use Webwizardsusa\Larafeed\ITunes\ITunesCategories;

trait HasITunesCategories
{
    protected ITunesCategories $iTunesCategories;

    public function iTunesCategory(mixed ...$categories): static
    {
        $this->iTunesCategories->add(...$categories);

        return $this;
    }

    public function getITunesCategories(): ITunesCategories
    {
        return $this->iTunesCategories;
    }

    public function hasITunesCategories(): bool
    {
        return ! $this->iTunesCategories->empty();
    }
}
