<?php

namespace Tonis\Di\TestAsset;

use Interop\Container\ContainerInterface;
use Tonis\Di\ServiceDecoratorInterface;

class TestDecorator implements ServiceDecoratorInterface
{
    /**
     * {@inheritDoc}
     */
    public function decorateService(ContainerInterface $di, $instance)
    {
        $instance->didItWork = true;
    }
}
