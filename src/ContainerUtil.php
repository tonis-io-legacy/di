<?php

namespace Tonis\Di;

abstract class ContainerUtil
{
    /**
     * @param Container $di
     * @param mixed $input
     * @return mixed
     */
    final public static function get(Container $di, $input)
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
