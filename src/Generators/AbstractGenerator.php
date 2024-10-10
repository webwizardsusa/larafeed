<?php

namespace Webwizardsusa\Larafeed\Generators;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Webwizardsusa\Larafeed\Channel;

abstract class AbstractGenerator
{
    protected Channel $channel;

    public function __construct(Channel $channel)
    {
        $this->channel = $channel;
    }

    abstract public function render(): mixed;

    abstract public function contentType(): string;

    public function save(string $path, ?string $disk = null): static
    {
        $rendered = $this->render();
        if ($disk) {
            Storage::disk($disk)->put($path, $rendered);
        } else {
            File::put($path, $rendered);
        }

        return $this;
    }
}
