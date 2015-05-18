<?php

namespace Tonis\Di;

trait ContainerAwareTrait
{
    /**
     * @var \Tonis\Di\Container|null
     */
    private $container;

    /**
     * @return \Tonis\Di\Container
     */
    public function di()
    {
        if (!$this->container instanceof Container) {
            $this->container = new Container();
        }
        return $this->container;
    }
}
