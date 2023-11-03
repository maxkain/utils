<?php

namespace App\Utils\FieldNormalizer;

abstract class AbstractFieldNormalizer implements FieldNormalizerInterface
{
    public static function getKey(): string
    {
        return static::class;
    }
}
