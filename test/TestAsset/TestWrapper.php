<?php

namespace Tonis\Di\TestAsset;

use Tonis\Di\Container;
use Tonis\Di\ServiceWrapperInterface;

class TestWrapper implements ServiceWrapperInterface
{
    /**
     * {@inheritDoc}
     */
    public function wrapService(Container $i, $name, $callable)
    {
        $instance = $callable();
        $wrapped = new \StdClass();
        $wrapped->original = $instance;
        $wrapped->name = $name;
        $wrapped->didItWork = true;

        return $wrapped;
    }
}
