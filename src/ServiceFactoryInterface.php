<?php

namespace Tonis\Di;

interface ServiceFactoryInterface
{
    /**
     * @param Container $di
     * @return mixed
     */
    public function createService(Container $di);
}
