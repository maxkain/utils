<?php

namespace App\Utils\FieldNormalizer;

use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;

#[Autoconfigure(['app.field_normalizer'], lazy: true)]
interface FieldNormalizerInterface
{
    public static function getKey(): string;
    public function normalize(mixed $value): mixed;
    public function denormalize(mixed $value): mixed;
}
