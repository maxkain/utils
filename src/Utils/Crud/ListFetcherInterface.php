<?php

namespace App\Utils\Crud;

interface ListFetcherInterface
{
    public function getList(mixed $filterDto, int $pageSize): array;
    public function getListNumResults(mixed $filterDto): int;
}
