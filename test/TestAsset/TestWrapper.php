<?php

namespace Tonis\Di\TestAsset;

use Interop\Container\ContainerInterface;
use Tonis\Di\ServiceWrapperInterface;

class TestWrapper implements ServiceWrapperInterface
{
    /**
     * {@inheritDoc}
     */
    public function wrapService(ContainerInterface $di, $name, $callable)
    {
        $instance = $callable();
        $wrapped = new \StdClass();
        $wrapped->original = $instance;
        $wrapped->name = $name;
        $wrapped->didItWork = true;

        return $wrapped;
    }
}
