<?php

namespace Tonis\Di\TestAsset;

use Interop\Container\ContainerInterface;
use Tonis\Di\ServiceFactoryInterface;

class TestFactory implements ServiceFactoryInterface
{
    /**
     * {@inheritDoc}
     */
    public function createService(ContainerInterface $di)
    {
        return new \StdClass();
    }
}
