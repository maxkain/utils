<?php

declare(strict_types=1);

namespace App\Utils\Crud;

interface FieldsConfiguratorInterface
{
    public function configureFormFields(): array;
    public function configureListFields(): array;
}
