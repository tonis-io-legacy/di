<?php

namespace Tonis\Di;

interface ContainerAwareInterface
{
    /**
     * @return \Tonis\Di\Container
     */
    public function di();
}