<?php

namespace Tonis\Di;

use Interop\Container\ContainerInterface;

interface ContainerAwareInterface
{
    /**
     * @param ContainerInterface $di
     * @return void
     */
    public function setDi(ContainerInterface $di);
}
