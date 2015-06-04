<?php

namespace Tonis\Di\Exception;

class InvalidServiceException extends \InvalidArgumentException
{
    /**
     * @param string $name
     * @param mixed $spec
     */
    public function __construct($name, $spec)
    {
        parent::__construct(sprintf(
            'Creating service "%s" failed: "%s" was an invalid spec',
            $name,
            is_object($spec) ? get_class($spec) : $spec
        ));
    }
}
