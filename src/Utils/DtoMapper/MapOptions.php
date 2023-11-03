<?php

namespace App\Utils\DtoMapper;

class MapOptions
{
    /**
     * @param array $ignoredAttributes
     * @param array<string, callable> $callbacks
     * @param array<string> $fieldNormalizers
     * @param array<string> $fieldDenormalizers
     */
    public function __construct(
        private readonly array $ignoredAttributes = [],
        private readonly array $callbacks = [],
        private readonly ?string $timezone = null
    ) {
    }

    public function getIgnoredAttributes(): array
    {
        return $this->ignoredAttributes;
    }

    public function getCallbacks(): array
    {
        return $this->callbacks;
    }

    public function getTimezone(): ?string
    {
        return $this->timezone;
    }
}
