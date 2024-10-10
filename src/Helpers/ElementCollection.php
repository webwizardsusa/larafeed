<?php

namespace Webwizardsusa\Larafeed\Helpers;

use ArrayAccess;
use ArrayIterator;
use Closure;
use Countable;
use Illuminate\Contracts\Support\Arrayable;
use InvalidArgumentException;
use IteratorAggregate;
use Webwizardsusa\Larafeed\Concerns\EvaluatesCallbacks;
use Webwizardsusa\Larafeed\Contracts\Element;

class ElementCollection implements ArrayAccess, Countable, IteratorAggregate, Arrayable
{
    use EvaluatesCallbacks;

    protected array $elements;

    public function __construct(array $elements = [])
    {
        $this->elements = $elements;
    }

    public function add(Element|array $element): static
    {
        if (is_array($element)) {
            foreach ($element as $item) {
                $this->add($item);
            }
        } else {
            $this->elements[] = $element;
        }

        return $this;
    }

    public function addIf(mixed $condition, Closure|Element $element): static
    {
        $value = $this->evaluateCallback($condition);
        if (! empty($value)) {
            $element = $this->evaluateCallback($element, $value);
            if ($element) {
                $this->add($element);
            }
        }

        return $this;
    }

    public function findByTag(string $tag): array
    {
        return array_values(array_filter($this->elements, function (Element $element) use ($tag) {
            return $element->getTag() === $tag;
        }));
    }

    /**
     * @param Closure $callback
     * @return array|Element[]
     */
    public function findBy(Closure $callback): array
    {
        $results = [];
        foreach ($this->elements as $element) {
            if ($callback($element)) {
                $results[] = $element;
            }
        }

        return $results;
    }

    public function remove(Element|Closure $element): static
    {
        if ($element instanceof Closure) {
            $this->elements = array_values(array_filter($this->elements, function ($item) use ($element) {
                $result = $this->evaluateCallback($element, $item);

                return ! $result;
            }));
        } else {
            $this->elements = array_values(array_filter($this->elements, function (Element $item) use ($element) {
                return $item !== $element;
            }));
        }

        return $this;
    }

    public function all(): array
    {
        return $this->elements;
    }

    public function offsetExists($offset): bool
    {
        return isset($this->elements[$offset]);
    }

    public function offsetGet($offset): mixed
    {
        return $this->elements[$offset];
    }

    public function offsetSet($offset, $value): void
    {
        if (! ($value instanceof Element)) {
            throw new InvalidArgumentException('Value must be an instance of Element');
        }
        if ($offset === null) {
            $this->elements[] = $value; // Add to the end if no index is specified
        } else {
            $this->elements[$offset] = $value;
        }
    }

    public function offsetUnset($offset): void
    {
        unset($this->elements[$offset]);
    }

    public function count(): int
    {
        return count($this->elements);
    }

    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->elements);
    }

    public function toArray()
    {
        return collect($this->elements)
            ->toArray();
    }
}
