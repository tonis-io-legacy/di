<?php

namespace Tonis\Di;

interface ServiceWrapperInterface
{
    /**
     * Wraps the provided instance.
     *
     * @param Container $i
     * @param string $name
     * @param callable $callable
     * @return mixed
     */
    public function wrapService(Container $i, $name, $callable);
}
