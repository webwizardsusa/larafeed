<?php

namespace Webwizardsusa\Larafeed\ITunes;

use Traversable;

class ITunesCategories implements \IteratorAggregate
{
    protected array $items = [];

    public function add(mixed ...$categories): static
    {
        foreach ($categories as $category) {
            if (is_array($category)) {
                return $this->add(...$category);
            }

            if (is_string($category)) {
                if (! class_exists($category) || ! method_exists($category, 'name')) {
                    throw new \Exception('ITunes categories must be an enum with a static name() method');
                }
            } elseif (is_object($category)) {
                $class = get_class($category);
                $parent = forward_static_call([$category, 'name']);
                if (! enum_exists($class) || ! method_exists($class, 'name')) {
                    throw new \Exception('ITunes categories must be an enum with a static name() method');
                }
            }

            if (! in_array($category, $this->items)) {
                $this->items[] = $category;
            }
        }

        return $this;
    }

    public function empty(): bool
    {
        return empty($this->items);
    }

    public function toArray(): array
    {
        $results = [];
        foreach ($this->items as $item) {
            $name = $this->getCategoryName($item);
            if (! isset($results[$name])) {
                $results[$name] = [
                    'category' => $name,
                    'children' => [],
                ];
            }

            if (is_object($item)) {
                $value = $item->value;
                if (! in_array($value, $results[$name]['children'])) {
                    $results[$name]['children'][] = $value;
                }
            }
        }

        return array_values($results);
    }

    private function getCategoryName($item): string
    {
        if (is_string($item)) {
            return forward_static_call([$item, 'name']);
        }

        if (! is_object($item) || ! method_exists($item, 'name')) {
            throw new \Exception('ITunes categories must be an enum with a static name() method');
        }

        return forward_static_call([get_class($item), 'name']);
    }

    public function getIterator(): Traversable
    {
        return new \ArrayIterator($this->toArray());
    }
}
