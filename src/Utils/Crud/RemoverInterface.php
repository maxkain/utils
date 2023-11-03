<?php

namespace App\Utils\Crud;

interface RemoverInterface
{
    public function remove(mixed $entity): void;
}
