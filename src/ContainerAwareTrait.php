<?php

namespace Tonis\Di;

trait ContainerAwareTrait
{
    /**
     * @var \Tonis\Di\Container|null
     */
    private $di;

    /**
     * @return \Tonis\Di\Container
     */
    public function di()
    {
        if (!$this->di instanceof Container) {
            $this->setDi(new Container);
        }
        return $this->di;
    }

    /**
     * @param Container $di
     */
    public function setDi(Container $di)
    {
        $this->di = $di;
    }
}
