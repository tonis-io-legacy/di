<?php

namespace Tonis\Di;

use Interop\Container\ContainerInterface;

interface ServiceDecoratorInterface
{
    /**
     * Decorates the provided instance.
     *
     * @param ContainerInterface $di
     * @param mixed $instance
     * @return mixed
     */
    public function decorateService(ContainerInterface $di, $instance);
}
