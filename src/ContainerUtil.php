<?php

namespace Tonis\Di;

abstract class ContainerUtil
{
    /**
     * @param Container $i
     * @param mixed $input
     * @return mixed
     */
    final public static function get(Container $i, $input)
    {
        if (is_string($input)) {
            if ($i->has($input)) {
                return $i->get($input);
            }

            if (class_exists($input)) {
                return new $input();
            }
        }
        return null;
    }
}
