<?php

namespace Tonis\Di\TestAsset;

use Tonis\Di\Container;
use Tonis\Di\ServiceFactoryInterface;

class TestFactory implements ServiceFactoryInterface
{
    /**
     * {@inheritDoc}
     */
    public function createService(Container $i)
    {
        return new \StdClass();
    }
}
