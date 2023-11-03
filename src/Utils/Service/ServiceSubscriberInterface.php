<?php

namespace App\Domain\Utility\Service;

use Symfony\Contracts\Service\ServiceSubscriberInterface as SymfonyServiceSubscriberInterface;

interface ServiceSubscriberInterface extends SymfonyServiceSubscriberInterface
{
    public static function getSubscribedServices(): array;
}
