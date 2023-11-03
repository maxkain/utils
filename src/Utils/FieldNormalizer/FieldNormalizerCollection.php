<?php

namespace App\Utils\FieldNormalizer;

use Symfony\Component\DependencyInjection\Attribute\TaggedIterator;

class FieldNormalizerCollection
{
    /** @var array<FieldNormalizerInterface> */
    private array $collection;

    public function __construct(
        #[TaggedIterator('app.field_normalizer', defaultIndexMethod: 'getKey')]
        iterable $collection
    ) {
        $this->collection = $collection instanceof \Traversable ? iterator_to_array($collection) : $collection;
    }

    public function get(string $key): ?FieldNormalizerInterface
    {
        return $this->collection[$key] ?? null;
    }
}
