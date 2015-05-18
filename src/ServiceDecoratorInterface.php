<?php

namespace Tonis\Di;

interface ServiceDecoratorInterface
{
    /**
     * Decorates the provided instance.
     *
     * @param Container $i
     * @param mixed $instance
     * @return mixed
     */
    public function decorateService(Container $i, $instance);
}
