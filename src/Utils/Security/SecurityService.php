<?php

namespace App\Utils\Security;

use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class SecurityService
{
    public function __construct(
        private TokenStorageInterface $tokenStorage,
        private AuthorizationCheckerInterface $authorizationChecker,
    )
    {
    }

    public function getCurrentUser(): mixed
    {
        $user = $this->tokenStorage->getToken()?->getUser();

        return $user instanceof User ? $user : null;
    }

    public function isGranted(mixed $attribute, mixed $subject = null): bool
    {
        return $this->authorizationChecker->isGranted($attribute, $subject);
    }

    public function denyAccessUnlessGranted(mixed $attribute, mixed $subject = null, ?string $message = null): void
    {
        if (!$this->isGranted($attribute, $subject)) {
            $message = $message ?? 'Access Denied.';
            $exception = new AccessDeniedException($message);
            $exception->setAttributes([$attribute]);
            $exception->setSubject($subject);

            throw $exception;
        }
    }
}
