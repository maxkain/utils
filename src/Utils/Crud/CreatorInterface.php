<?php

namespace App\Utils\Crud;

interface CreatorInterface
{
    public function createEntityFromDto(object $dto, bool $flush): object;
    public function getDtoClass(): string;
}
