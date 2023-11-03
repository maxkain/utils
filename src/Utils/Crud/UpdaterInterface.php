<?php

namespace App\Utils\Crud;

interface UpdaterInterface
{
    public function createDtoFromEntity(object $entity): object;
    public function updateEntityFromDto(object $dto, object $entity, bool $flush): object;
    public function findEntity(mixed $id): ?object;
}
