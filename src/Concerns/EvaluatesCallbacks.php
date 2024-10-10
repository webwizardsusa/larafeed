<?php

namespace Webwizardsusa\Larafeed\Concerns;

use Closure;

trait EvaluatesCallbacks
{
    public function evaluateCallback(mixed $callback, ...$args): mixed
    {
        if (! $callback instanceof Closure) {
            return $callback;
        }

        return $callback(...$args);
    }
}
