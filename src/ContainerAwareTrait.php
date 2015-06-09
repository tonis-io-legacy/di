<?php

namespace Tonis\Di;

use Interop\Container\ContainerInterface;

trait ContainerAwareTrait
{
    /** @var ContainerInterface|null */
    private $di;

    /**
     * @return \Tonis\Di\Container
     */
    public function di()
    {
        if (!$this->di instanceof ContainerInterface) {
            $this->setDi(new Container);
        }
        return $this->di;
    }

    /**
     * @param ContainerInterface $di
     */
    public function setDi(ContainerInterface $di)
    {
        $this->di = $di;
    }
}
