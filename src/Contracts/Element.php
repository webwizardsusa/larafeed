<?php

namespace Webwizardsusa\Larafeed\Contracts;

interface Element
{
    public function getTag(): string;

    public function setTag(string $tag): static;

    public function toArray(): array;
}
