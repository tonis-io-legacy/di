<?php

namespace Tonis\Di\TestAsset;

use Tonis\Di\Container;
use Tonis\Di\ServiceDecoratorInterface;

class TestDecorator implements ServiceDecoratorInterface
{
    /**
     * {@inheritDoc}
     */
    public function decorateService(Container $i, $instance)
    {
        $instance->didItWork = true;
    }
}
