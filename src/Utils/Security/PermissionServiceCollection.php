<?php

namespace App\Utils\Security;

use Symfony\Component\DependencyInjection\Attribute\TaggedIterator;

class PermissionServiceCollection
{
    /**
     * @var array<PermissionServiceInterface>
     */
    private array $collection;

    public function __construct(
        #[TaggedIterator('app.permission_service', defaultIndexMethod: 'getKey')]
        iterable $collection
    ) {
        $this->collection = $collection instanceof \Traversable ? iterator_to_array($collection) : $collection;
    }

    public function get($index): ?PermissionServiceInterface
    {
        return $this->collection[$index] ?? null;
    }
}
