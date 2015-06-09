<?php

namespace Tonis\Di;

use Interop\Container\ContainerInterface;

interface ServiceWrapperInterface
{
    /**
     * Wraps the provided instance.
     *
     * @param ContainerInterface $di
     * @param string $name
     * @param callable $callable
     * @return mixed
     */
    public function wrapService(ContainerInterface $di, $name, $callable);
}
