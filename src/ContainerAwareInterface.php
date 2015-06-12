<?php

namespace Tonis\Di;

use Interop\Container\ContainerInterface;

interface ContainerAwareInterface
{
    /**
     * @param ContainerInterface $serviceContainer
     * @return void
     */
    public function setServiceContainer(ContainerInterface $serviceContainer);
}
