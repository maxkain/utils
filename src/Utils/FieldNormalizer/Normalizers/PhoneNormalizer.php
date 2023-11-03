<?php

namespace App\Utils\FieldNormalizer\Normalizers;

use App\Utils\FieldNormalizer\AbstractFieldNormalizer;

class PhoneNormalizer extends AbstractFieldNormalizer
{
    public function normalize(mixed $value): ?string
    {
        $phone = preg_replace('/^\+7/', '', $value);
        $phone = preg_replace('/[^0-9]/', '', $phone);
        $phone = substr($phone, -10);

        return $phone ?? null;
    }

    public function denormalize(mixed $value): ?string
    {
        return preg_replace('/(\d\d\d)(\d\d\d)(\d\d)(\d\d)/', '+7 ($1) $2-$3-$4', $value);
    }
}
