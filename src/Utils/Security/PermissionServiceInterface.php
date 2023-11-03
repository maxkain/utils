<?php

namespace App\Utils\Security;

use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;

#[Autoconfigure(tags: ['app.permission_service'], lazy: true)]
interface PermissionServiceInterface
{
    public static function getKey(): string;
    public function getActions(): array;
    public function voteOnAttribute(string $action, object|string $subject);
}
