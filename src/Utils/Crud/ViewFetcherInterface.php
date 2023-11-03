<?php

namespace App\Utils\Crud;

interface ViewFetcherInterface
{
    public function getOne(mixed $entityId): ?object;
}
