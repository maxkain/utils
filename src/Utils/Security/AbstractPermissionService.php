<?php

namespace App\Utils\Security;

use App\Utils\Service\ServiceSubscriberInterface;
use App\Utils\Service\ServiceSubscriberTrait;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

abstract class AbstractPermissionService implements PermissionServiceInterface, ServiceSubscriberInterface
{
    use ServiceSubscriberTrait;

    public const CREATE = 'create';
    public const UPDATE = 'update';
    public const DELETE = 'delete';
    public const VIEW = 'view';
    public const LIST = 'list';
    public const ADMINISTRATION = 'administration';
    public const MANAGE = 'manage';

    public function getActions(): array
    {
        return [static::CREATE, static::UPDATE, static::DELETE, static::VIEW, static::LIST, static::ADMINISTRATION,
            static::MANAGE];
    }

    protected function _administration(): bool
    {
        return $this->isGranted(Employee::ROLE_ADMIN);
    }

    public static function getSubscribedServices(): array
    {
        return [
            AuthorizationCheckerInterface::class,
            TokenStorageInterface::class
        ];
    }

    public function voteOnAttribute(string $action, object|string $subject): bool
    {
        $method = '_' . $action;
        if (method_exists($this, $method)) {
            return call_user_func([$this, $method], $subject);
        }

        return $this->defaultVote($action, $subject);
    }

    protected function defaultVote(string $action, object|string $subject): bool
    {
        return true;
    }

    protected function isGranted(string|array $attribute, mixed $subject = null): bool
    {
        $authorizationChecker = $this->container->get(AuthorizationCheckerInterface::class);
        $attributes = is_string($attribute) ? [$attribute] : $attribute;

        foreach ($attributes as $item) {
            if ($authorizationChecker->isGranted($item, $subject)) {
                return true;
            }
        }

        return false;
    }

    protected function getCurrentUser(): mixed
    {
        return $this->container->get(TokenStorageInterface::class)->getCurrentUser();
    }
}
