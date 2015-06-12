<?php

namespace Tonis\Di;

use Interop\Container\ContainerInterface;

trait ContainerAwareTrait
{
    /** @var ContainerInterface|null */
    private $serviceContainer;

    /**
     * @return \Tonis\Di\Container
     */
    public function getServiceContainer()
    {
        if (!$this->serviceContainer instanceof ContainerInterface) {
            $this->setServiceContainer(new Container);
        }
        return $this->serviceContainer;
    }

    /**
     * @param ContainerInterface $serviceContainer
     */
    public function setServiceContainer(ContainerInterface $serviceContainer)
    {
        $this->serviceContainer = $serviceContainer;
    }
}
