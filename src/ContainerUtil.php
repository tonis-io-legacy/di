<?php

namespace Tonis\Di;

use Interop\Container\ContainerInterface;

abstract class ContainerUtil
{
    /**
     * @param ContainerInterface $di
     * @param mixed $input
     * @return mixed
     */
    final public static function get(ContainerInterface $di, $input)
    {
        if (is_string($input)) {
            if ($di->has($input)) {
                return $di->get($input);
            }

            if (class_exists($input)) {
                return new $input();
            }
        }
        return $input;
    }
}
