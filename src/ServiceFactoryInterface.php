<?php

namespace Tonis\Di;

use Interop\Container\ContainerInterface;

interface ServiceFactoryInterface
{
    /**
     * @param ContainerInterface $di
     * @return mixed
     */
    public function createService(ContainerInterface $di);
}
