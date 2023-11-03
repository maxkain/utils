<?php

namespace App\Domain\Utility\Service;

use Psr\Container\ContainerInterface;
use Symfony\Contracts\Service\Attribute\Required;

trait ServiceSubscriberTrait
{
    private ContainerInterface $container;

    #[Required]
    public function setContainer(ContainerInterface $container): void
    {
        $this->container = $container;
    }
}
